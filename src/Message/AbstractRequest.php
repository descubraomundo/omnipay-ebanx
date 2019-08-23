<?php

namespace Omnipay\Ebanx\Message;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Abstract Request
 *
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    protected $liveEndpoint = 'https://api.example.com';
    protected $testEndpoint = 'https://staging.ebanx.com.br/ws';

    /**
     * Get the gateway Integration Key.
     *
     * Authentication is by means of a single secret API key set as
     * the integrationKey parameter when creating the gateway object.
     *
     * @return string
     */
    public function getIntegrationKey()
    {
        return $this->getParameter('integration_key');
    }

    /**
     * Set Integration key
     *
     * @param string $value
     * @return AbstractRequest provides a fluent interface.
     */
    public function setIntegrationKey($value)
    {
        return $this->setParameter('integration_key', $value);
    }

    /**
     * Get the boleto due date
     *
     * @return string boleto due date
     */
    public function getBoletoDueDate($format = 'd/m/Y')
    {
        $value = $this->getParameter('boletoDueDate');

        return $value ? $value->format($format) : null;
    }

    /**
     * Set the boleto due date
     *
     * @param string $value defaults to atual date + 30 days
     * @return AbstractRequest
     */
    public function setBoletoDueDate($value)
    {
        if ($value) {
            $value = new \DateTime($value, new \DateTimeZone('UTC'));
            $value = new \DateTime($value->format('Y-m-d\T03:00:00'), new \DateTimeZone('UTC'));
        } else {
            $value = null;
        }

        return $this->setParameter('boletoDueDate', $value);
    }

    /**
     * Get Document number (CPF or CNPJ).
     *
     * @return string
     */
    public function getDocumentNumber()
    {
        return $this->getParameter('documentNumber');
    }

    /**
     * Set Document Number (CPF or CNPJ)
     *
     * Non-numeric characters are stripped out of the document number, so
     * it's safe to pass in strings such as "224.158.178-40" etc.
     *
     * @param string $value Parameter value
     * @return AbstractRequest
     */
    public function setDocumentNumber($value)
    {
        // strip non-numeric characters
        return $this->setParameter('documentNumber', preg_replace('/\D/', '', $value));
    }

    /**
     * Get the type of person that is making the payment
     * This allow to a payment be made by a company
     *
     * @return string
     */
    public function getPersonType()
    {
        return $this->getParameter('personType') ?: 'personal';
    }

    /**
     * Set Person Type
     * @param string $value Person type value
     * @return AbstractRequest
     */
    public function setPersonType($value)
    {
        return $this->setParameter('personType', $value);
    }

    /**
     * Get the company name that is making the payment
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->getParameter('companyName');
    }

    /**
     * Set Company Name
     * @param string $value Company name value
     * @return AbstractRequest
     */
    public function setCompanyName($value)
    {
        return $this->setParameter('companyName', $value);
    }

    /**
     * Get the split param
     *
     * @return array
     */
    public function getSplit()
    {
        return $this->getParameter('split') ?: [];
    }

    /**
     * Set Split
     * @param array $value Array containing the required fields to split the payment
     * @return AbstractRequest
     */
    public function setSplit($value = [])
    {
        return $this->setParameter('split', $value);
    }

    /**
     * A note about the payment.
     *
     * @return string
     */
    public function getNote()
    {
        return $this->getParameter('note');
    }

    /**
     * Set a note about the payment. The value of this parameter
     * will be shown along with payment details.
     * @param string $value Person type value
     * @return AbstractRequest
     */
    public function setNote($value)
    {
        return $this->setParameter('note', $value);
    }

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string the HTTP method
     */
    public function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        $response = $this->httpClient->request(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            $this->getHeaders(),
            json_encode($data)
        );

        $payload =  json_decode($response->getBody()->getContents(), true);

        return $this->createResponse($payload);
    }

    /**
     * Get the endpoint where the request should be made.
     *
     * @return string the URL of the endpoint
     */
    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    protected function getHeaders()
    {
        return [];
    }

    /**
     * Get the base data.
     *
     * Because the Ebanx gateway requires a common of fields for every request
     * this function can be called to this common data in the format that the
     * API requires.
     *
     * @return array
     */
    protected function getDefaultParameters()
    {
        $data                    = array();
        $data['integration_key'] = $this->getIntegrationKey();

        return $data;
    }

    /**
     * Get the payment data.
     *
     * Because the Ebanx gateway uses a common format for passing
     * payment data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array
     */
    protected function getPaymentData($aditionalPaymentData = [])
    {
        $customerData = $this->getCustomerData();
        $addressData  = $this->getAddressData();
        $splitData    = $this->getSplitData();

        $paymentData                          = array();
        $paymentData['merchant_payment_code'] = $this->getTransactionId();
        $paymentData['currency_code']         = $this->getCurrency();
        $paymentData['amount_total']          = $this->getAmount();
        $paymentData['payment_type_code']     = $this->getPaymentMethod();

        if($notifyUrl = $this->getNotifyUrl()) {
            $paymentData['notification_url']      = $notifyUrl;
        }
        if($returnUrl = $this->getReturnUrl()) {
            $paymentData['redirect_url']      = $returnUrl;
        }

        if($paymentNote = $this->getNote()) {
            $paymentData['note']      = $paymentNote;
        }

        $paymentData = array_merge(
            $customerData,
            $addressData,
            $paymentData,
            $splitData,
            $aditionalPaymentData
        );

        return ['payment' => $paymentData];
    }

    /**
     * Get the customer data.
     *
     * Because the Ebanx gateway uses a common format for passing
     * customer data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array
     */
    protected function getCustomerData()
    {
        $this->validate('documentNumber');
        $card    = $this->getCard();

        $data                 = array();
        $data['name']         = $card->getName();
        $data['email']        = $card->getEmail();
        $data['document']     = $this->getDocumentNumber();
        $data['phone_number'] = $card->getPhone();

        $personType = $this->getPersonType();
        //If you need to create a payment for a company a couple of extra parameters are need.
        if($personType == 'business') {
            $this->validate('companyName');
            $data['name']                = $this->getCompanyName();
            $data['person_type']         = $personType;
            $data['responsible']['name'] = $card->getName();
        }
        return $data;
    }

    /**
     * Get the card data.
     *
     * Because the Ebanx gateway uses a common format for passing
     * card data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array
     */
    protected function getCardData()
    {
        $card                = $this->getCard();
        $cardReference       = $this->getCardReference();
        $data                = array();
        $data['instalments'] = $this->getInstallments();

        // We try first for the card reference
        if($cardReference) {
            $data['creditcard']['token']  = $cardReference;
        } elseif($card) {
            $card->validate();
            $data['creditcard']['card_name']     = $card->getName();
            $data['creditcard']['card_number']   = $card->getNumber();
            $data['creditcard']['card_due_date'] = $card->getExpiryMonth() . '/' . $card->getExpiryYear();
            if ($card->getCvv()) {
                $data['creditcard']['card_cvv']  = $card->getCvv();
            }
        }

        return $data;
    }
    /**
     * Get the boleto data.
     *
     * Because the Ebanx gateway uses a common format for passing
     * boleto data to the API, this function can be called to get the
     * data from the associated request object in the format that the
     * API requires.
     *
     * @return array
     */
    protected function getBoletoData()
    {
        $this->validate('boletoDueDate');

        $data                  = array();
        $data['due_date'] = $this->getBoletoDueDate();
        return $data;
    }

    /**
     * Get the address data.
     *
     * Because the Ebanx gateway uses a common format for passing
     * address data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array
     */
    protected function getAddressData()
    {
        $card    = $this->getCard();
        $address = array_map('trim', explode(',', $card->getAddress1()));

        $data                  = array();
        $data['address']       = $address[0];
        $data['street_number'] = isset($address[1]) ? $address[1] : '';
        $data['street_complement'] = isset($address[2]) ? $address[2] : '';
        $data['city']          = $card->getCity();
        $data['state']         = $card->getState();
        $data['country']       = $card->getCountry();
        $data['zipcode']       = $card->getPostcode();

        return $data;
    }

    /**
     * Get the split data.
     *
     * Because the Ebanx gateway uses a common format for passing
     * split payment data to the API, this function can be called to get the
     * data from the associated request in the format that the
     * API requires.
     *
     * @return array
     */
    protected function getSplitData()
    {
        $split = $this->getSplit();
        return !empty($split) ? ['split' => $split] : [];
    }

    /**
     * Get installments.
     *
     * @return integer the number of installments
     */
    public function getInstallments()
    {
        return $this->getParameter('installments') ?: 1;
    }

    /**
     * Set Installments.
     *
     * The number must be between 1 and 12.
     * If the payment method is boleto defaults to 1.
     *
     * @param integer $value
     * @return AuthorizeRequest provides a fluent interface.
     */
    public function setInstallments($value)
    {
        return $this->setParameter('installments', (int) $value);
    }
}
