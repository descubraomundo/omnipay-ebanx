<?php

namespace Omnipay\Ebanx\Message;

use League\Ebanx\Test\TestCase;

class PaymentPageRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = new PaymentPageRequest($this->getHttpClient(), $this->getHttpRequest());


        $this->request->initialize(
            array(
                'amount' => '12.00',
                'card' => $this->getValidCard(),
                'currency' => 'BRL',
                'transactionId' => 'ecc9be4512a',
            )
        );
    }

    public function testGetData()
    {
        $data    = $this->request->getData();

        // Check required params
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertArrayHasKey('country', $data);
        $this->assertArrayHasKey('payment_type_code', $data);
        $this->assertArrayHasKey('merchant_payment_code', $data);
        $this->assertArrayHasKey('currency_code', $data);
        $this->assertArrayHasKey('amount', $data);
    }


    public function testSendSuccess()
    {
        $this->setMockHttpResponse('PaymentPageSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('https://staging.ebanx.com.br/checkout/?hash=5ae0b5d4f1883ed4b214c0277af29f1981443f59a26eef87', $response->getRedirectUrl());
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.ebanx.com.br/ws/request', $this->request->getEndpoint());
    }
}

