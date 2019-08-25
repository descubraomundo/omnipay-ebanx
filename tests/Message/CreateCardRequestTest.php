<?php

namespace Omnipay\Ebanx\Message;

use League\Ebanx\Test\TestCase;

class CreateCardRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array('card' => $this->getValidCard())
        );
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.ebanx.com.br/ws/token', $this->request->getEndpoint());
    }

    public function testGetData()
    {
        $data    = $this->request->getData();

        // Check required params
        $this->assertArrayHasKey('integration_key', $data);
        $this->assertArrayHasKey('country', $data);
        $this->assertArrayHasKey('payment_type_code', $data);

        $cardData = $this->request->getCardData();
        $this->assertArrayHasKey('card_number', $cardData['creditcard']);
        $this->assertArrayHasKey('card_name', $cardData['creditcard']);
        $this->assertArrayHasKey('card_due_date', $cardData['creditcard']);
        $this->assertArrayHasKey('card_cvv', $cardData['creditcard']);
    }
}

