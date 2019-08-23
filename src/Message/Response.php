<?php

namespace Omnipay\Ebanx\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;


/**
 * Ebanx Response
 *
 * This is the response class for all Ebanx requests.
 *
 * @see \Omnipay\Ebanx\Gateway
 */
class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    /**
     * Is the transaction a redirect?
     *
     * @return bool
     */
    public function isRedirect()
    {
        return isset($this->data['redirect_url']);
    }

    /**
     * Get the redirect url from the response.
     *
     * Returns null if the request was not a redirect.
     *
     * @return string|null
     */
    public function getRedirectUrl()
    {
        if (isset($this->data['redirect_url'])) {
            return $this->data['redirect_url'];
        }

        return null;
    }

    /**
     * Is the transaction successful?
     *
     * @return bool
     */
    public function isSuccessful()
    {

        return $this->data['status'] == "SUCCESS";
    }

    /**
     * Get the transaction reference.
     *
     * @return string|null
     */
    public function getTransactionReference()
    {
        if (isset($this->data['payment']['hash'])) {
            return $this->data['payment']['hash'];
        }

        return null;
    }
    /**
     * Get the transaction id.
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        if (isset($this->data['payment']['merchant_payment_code'])) {
            return $this->data['payment']['merchant_payment_code'];
        }

        return null;
    }

    /**
     * Get the error message from the response.
     *
     * Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getMessage()
    {
        if (!$this->isSuccessful()) {
            return '[' . $this->data['status_code'] . '] ' . $this->data['status_message'];
        } elseif($boletoData = $this->getBoleto()) {
            return $boletoData['boleto_barcode'];
        } elseif($transactionStatus = $this->getTransactionStatus()) {
            return '[' . $transactionStatus['code'] . '] ' .$transactionStatus['description'];
        }

        return null;

    }

    /**
     * Get a cardReference, from the createCard requests.
     *
     * @return string|null
     */
    public function getCardReference()
    {
        if (isset($this->data['token'])) {
            return $this->data['token'];
        }

        return null;
    }

    /**
     * Get all paymentData, from the requests.
     *
     * @return string|null
     */
    public function getPaymentData($key = null)
    {
        if (isset($this->data['payment'])) {

            if($key && isset($this->data['payment'][$key])) {
                return $this->data['payment'][$key];
            } else {
                return null;
            }

            return $this->data['payment'];
        }

        return null;
    }

    /**
     * Get the boleto_url, boleto_barcode and boleto_expiration_date in the
     * transaction object.
     *
     * @return array|null the boleto_url, boleto_barcode and boleto_expiration_date
     */
    public function getBoleto()
    {
        $data = null;

        if (isset($this->data['payment']['boleto_url'])) {
            $data = array(
                'boleto_url'             => $this->data['payment']['boleto_url'],
                'boleto_barcode'         => $this->data['payment']['boleto_barcode'],
                'boleto_expiration_date' => $this->data['payment']['due_date'],
            );
        }

        return $data;
    }

    /**
     * Get the payment status out of the response array
     *
     * @return string
     */
    public function getPaymentStatus()
    {

        return $this->getPaymentData('status');
    }

    /**
     * Get the transaction status out of the response array
     *
     * @return array|null
     */
    public function getTransactionStatus()
    {

        return $this->getPaymentData('transaction_status');
    }

    /**
     * Get the refunds propeties out of the response array
     *
     * @return array|null
     */
    public function getRefunds()
    {

        return $this->getPaymentData('refunds');
    }

    /**
     * Get the Calculted Installments provided by Ebanx API.
     *
     * @return array|null the calculated installments
     */
    public function getCalculatedInstallments()
    {
        if (isset($this->data['installments'])) {
            $data = $this->data['installments'];
            return $data;
        } else {
            return null;
        }
    }

}
