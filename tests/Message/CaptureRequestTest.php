<?php

namespace Omnipay\Ebanx\Message;

use League\Ebanx\Test\TestCase;

class CaptureRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testEndpointWithTransactionReference()
    {
        $this->request->setTransactionReference(111111);
        $this->assertSame('https://api.ebanx.com.br/ws/capture?hash=111111', $this->request->getEndpoint());
    }

    public function testEndpointWithTransactionId()
    {
        $this->request->setTransactionId(111111);
        $this->assertSame('https://api.ebanx.com.br/ws/capture?merchant_payment_code=111111', $this->request->getEndpoint());
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('CaptureSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('5478ba283ef8674415082844ee14817376e49bb76aa9d4c7', $response->getTransactionReference());
        $this->assertSame('126378126', $response->getTransactionId());
        $this->assertSame('CO', $response->getPaymentStatus());
    }

    public function testSendError()
    {
        $this->setMockHttpResponse('CaptureFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertSame('[BP-CAP-4] Payment has already been captured, status is: CO', $response->getMessage());
    }
}

