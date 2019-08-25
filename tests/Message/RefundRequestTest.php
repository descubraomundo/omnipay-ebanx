<?php

namespace Omnipay\Ebanx\Message;

use League\Ebanx\Test\TestCase;

class RefundRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize(
            array(
                'transactionId' => 'transaction_id',
                'transactionReference' => 'transaction_reference',
                'amount' => 100,
                'description' => "Required Refund Description",
            )
        );
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.ebanx.com.br/ws/refund?operation=request&hash=transaction_reference&merchant_refund_code=transaction_id&amount=100.00&description=Required+Refund+Description', $this->request->getEndpoint());
    }

    public function testGetDataIsEmpty()
    {
        $data    = $this->request->getData();
        $this->assertEmpty($data);
    }
}
