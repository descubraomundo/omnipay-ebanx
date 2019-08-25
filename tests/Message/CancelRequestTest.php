<?php

namespace Omnipay\Ebanx\Message;

use League\Ebanx\Test\TestCase;

class CancelRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = new CancelRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize(
            array('transactionReference' => 'ecc9be4512a')
        );
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.ebanx.com.br/ws/cancel?hash=ecc9be4512a', $this->request->getEndpoint());
    }


    public function testGetDataIsEmpty()
    {
        $data    = $this->request->getData();

        // Check required params
        $this->assertEmpty($data);
    }
}

