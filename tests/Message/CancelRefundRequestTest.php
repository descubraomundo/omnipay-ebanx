<?php

namespace Omnipay\Ebanx\Message;

use League\Ebanx\Test\TestCase;

class CancelRefundRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = new CancelRefundRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize(
            array('transactionId' => 'ecc9be4512a')
        );
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.ebanx.com.br/ws/refund?operation=cancel&merchant_refund_code=ecc9be4512a', $this->request->getEndpoint());
    }


    public function testGetDataIsEmpty()
    {
        $data    = $this->request->getData();
        $this->assertEmpty($data);
    }
}

