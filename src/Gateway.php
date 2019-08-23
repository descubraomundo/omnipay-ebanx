<?php

namespace Omnipay\Ebanx;

use Omnipay\Common\AbstractGateway;

/**
 * Ebanx Gateway
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Ebanx';
    }

    /**
     * Get the gateway parameters.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'integration_key' => '',
            'test_mode' => false,
        );
    }

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
     * @param  string $value
     * @return Gateway provides a fluent interface.
     */
    public function setIntegrationKey($value)
    {
        return $this->setParameter('integration_key', $value);
    }


    /**
     * Authorize Request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Ebanx\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ebanx\Message\AuthorizeRequest', $parameters);
    }


    /**
     * Capture Request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Ebanx\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ebanx\Message\CaptureRequest', $parameters);
    }

    /**
     * Cancel Request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Ebanx\Message\CancelRequest
     */
    public function cancel(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ebanx\Message\CancelRequest', $parameters);
    }

    /**
     * Refund Request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Ebanx\Message\RefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ebanx\Message\RefundRequest', $parameters);
    }

    /**
     * Cancel Refund Request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Ebanx\Message\CancelRefundRequest
     */
    public function cancelRefund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ebanx\Message\CancelRefundRequest', $parameters);
    }

    /**
     * Fetch Transaction Request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Ebanx\Message\FetchTransactionRequest
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ebanx\Message\FetchTransactionRequest', $parameters);
    }

    /**
     * Purchase Request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Ebanx\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ebanx\Message\PurchaseRequest', $parameters);
    }


    /**
     * Payment Page Request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Ebanx\Message\PaymentPageRequest
     */
    public function paymentPage(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ebanx\Message\PaymentPageRequest', $parameters);
    }

    /**
     * Create Card Request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Ebanx\Message\CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ebanx\Message\CreateCardRequest', $parameters);
    }
}
