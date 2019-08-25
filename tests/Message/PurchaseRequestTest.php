<?php

namespace Omnipay\Ebanx\Message;

use League\Ebanx\Test\TestCase;

class PurchaseRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());


        $this->request->initialize(
            array(
                'amount' => '12.00',
                'card' => $this->getValidCard(),
                'currency' => 'BRL',
                'paymentMethod' => 'creditcard',
                'transactionId' => 'ecc9be4512a',
                'documentNumber' => $this->getValidDocumentNumber(),
            )
        );
    }

    public function testCaptureIsTrue()
    {
        $data = $this->request->getData();
        $this->assertTrue($data['payment']['creditcard']['auto_capture']);
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.ebanx.com.br/ws/direct', $this->request->getEndpoint());
    }
}

