<?php

namespace Omnipay\Ebanx;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\CreditCard;

class GatewayTest extends GatewayTestCase
{
    /**
     *
     *
     * @var EbanxGateway
     */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = array(
            'amount' => '10.00',
            'card' => $this->getValidCard(),
        );
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Ebanx\Message\AuthorizeRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCapture()
    {
        $request = $this->gateway->capture();

        $this->assertInstanceOf('Omnipay\Ebanx\Message\CaptureRequest', $request);
    }

    public function testCancel()
    {
        $request = $this->gateway->cancel();

        $this->assertInstanceOf('Omnipay\Ebanx\Message\CancelRequest', $request);
    }

    public function testRefund()
    {
        $request = $this->gateway->refund(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Ebanx\Message\RefundRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCancelRefund()
    {
        $request = $this->gateway->cancelRefund(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Ebanx\Message\CancelRefundRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testFetchTransaction()
    {
        $request = $this->gateway->fetchTransaction();

        $this->assertInstanceOf('Omnipay\Ebanx\Message\FetchTransactionRequest', $request);
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Ebanx\Message\PurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testPaymentPage()
    {
        $request = $this->gateway->paymentPage(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Ebanx\Message\PaymentPageRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCreateCard()
    {
        $request = $this->gateway->createCard(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Ebanx\Message\CreateCardRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }
}
