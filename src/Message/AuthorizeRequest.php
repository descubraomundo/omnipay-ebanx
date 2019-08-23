<?php
/**
 * Ebanx Authorize Request
 */
namespace Omnipay\Ebanx\Message;
/**
 * Ebanx Authorize Request
 *
 * An Authorize request is similar to a purchase request but the
 * charge issues an authorization (or pre-authorization), and no money
 * is transferred.  The transaction will need to be captured later
 * in order to effect payment.
 *
 * The card object is required by default to get all the customer information,
 * even if you want to use boleto payment method. If you want you can pass the
 * cardReference paramenter
 *
 * Ebanx gateway supports only two types of "paymentMethod":
 *
 * * creditcard
 * * boleto
 *
 *
 * You must provide aditional customer details to process the request at Ebanx API
 * These details is passed using the following attributes
 *
 * * documentNumber (CPF or CNPJ)
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the Ebanx Gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('Ebanx');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(array(
 *       'integration_key' => 'MyApiKey',
 *   ));
 *
 *   // Create a credit card object
 *   // This card can be used for testing.
 *   $card = new CreditCard(array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'number'       => '4242424242424242',
 *               'expiryMonth'  => '01',
 *               'expiryYear'   => '2020',
 *               'cvv'          => '123',
 *               'email'        => 'customer@example.com',
 *               'address1'     => 'Street name, Street number, Complement',
 *               'address2'     => 'Neighborhood',
 *               'city'         => 'City',
 *               'state'        => 'sp',
 *               'country'      => 'BR',
 *               'postcode'     => '05443100',
 *               'phone'        => '19 3242 8855',
 *   ));
 *
 *   // Do an authorize transaction on the gateway
 *   $transaction = $gateway->authorize(array(
 *       'amount'           => '10.00',
 *       'documentNumber'   => '214.278.589-40',
 *       'note'             => 'test',
 *       'paymentMethod'    => 'creditcard',
 *       'card'             => $card,
 *       'currency'         => 'BRL',
 *   ));
 *
 *   $response = $transaction->send();
 *   if ($response->isRedirect()) {
 *       // redirect to offsite payment gateway
 *       $response->redirect();
 *   } elseif ($response->isSuccessful()) {
 *       echo "Authorize transaction was successful!\n";
 *       echo "Transaction reference = " . $response->getTransactionReference() . "\n";
 *   } else {
 *       // payment failed: display message to customer
 *       exit($response->getMessage());
 *   }
 * </code>
 *
 * @see  \Omnipay\Ebanx\Gateway
 * @see  \Omnipay\Ebanx\Message\CaptureRequest
 * @link https://developers.ebanxpagamentos.com/api-reference/ebanx-payment-api/ebanx-payment-guide/guide-create-a-payment/brazil/
 */

class AuthorizeRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('card', 'amount', 'currency', 'transactionId', 'documentNumber');

        $data                = $this->getDefaultParameters();
        $data['operation']   = 'request';
        $data['description'] = $this->getDescription();

        switch ($this->getPaymentMethod()) {
        case 'creditcard':
            $paymentData = $this->getPaymentData($this->getCardData());
            // As this is only the Autorize Request, we overwrite the auto capture to false
            $paymentData['payment']['creditcard']['auto_capture'] = false;
            break;
        case 'boleto':
            $paymentData = $this->getPaymentData($this->getBoletoData());
            break;
        default:
            $paymentData = $this->getPaymentData();
            break;
        }

        $data = array_merge($data, $paymentData);

        return $data;
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/direct';
    }
}
