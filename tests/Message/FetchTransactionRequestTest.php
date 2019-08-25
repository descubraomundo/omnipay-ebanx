<?php

namespace Omnipay\Ebanx\Message;

use League\Ebanx\Test\TestCase;

class FetchTransactionRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = new FetchTransactionRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testEndpointWithTransactionReference()
    {
        $this->request->initialize(
            array('transactionReference' => 'ecc9be4512a')
        );
        $this->assertSame('https://api.ebanx.com.br/ws/query?hash=' . $this->request->getTransactionReference(), $this->request->getEndpoint());
    }
    public function testEndpointWithTransactionId()
    {
        $this->request->initialize(
            array('transactionId' => 'ecc9be4512a')
        );
        $this->assertSame('https://api.ebanx.com.br/ws/query?merchant_payment_code=' . $this->request->getTransactionId(), $this->request->getEndpoint());
    }


    public function testGetDataIsEmpty()
    {
        $data    = $this->request->getData();
        $this->assertEmpty($data);
    }
}

