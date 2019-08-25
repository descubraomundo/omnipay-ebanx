<?php

namespace Omnipay\Ebanx\Message;

use League\Ebanx\Test\TestCase;

class AuthorizeRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => '12.00',
                'card' => $this->getValidCard(),
                'currency' => 'BRL',
                'paymentMethod' => '',
                'transactionId' => 'ecc9be4512a',
                'documentNumber' => $this->getValidDocumentNumber(),
            )
        );
    }

    public function testGetEndpoint()
    {
        $this->assertSame('https://api.ebanx.com.br/ws/direct', $this->request->getEndpoint());
    }

    public function testGetDataDefaulPaymentMethod()
    {
        $data    = $this->request->getData();
        $card    = $this->request->getCard();
        $address = $this->request->getAddressData();

        // Check required params
        $this->assertArrayHasKey('integration_key', $data);
        $this->assertArrayHasKey('operation', $data);
        $this->assertArrayHasKey('payment', $data);

        $paymentData = $data['payment'];
        $this->assertArrayHasKey('name', $paymentData);
        $this->assertArrayHasKey('email', $paymentData);
        $this->assertArrayHasKey('document', $paymentData);
        $this->assertArrayHasKey('address', $paymentData);
        $this->assertArrayHasKey('street_number', $paymentData);
        $this->assertArrayHasKey('city', $paymentData);
        $this->assertArrayHasKey('state', $paymentData);
        $this->assertArrayHasKey('zipcode', $paymentData);
        $this->assertArrayHasKey('country', $paymentData);
        $this->assertArrayHasKey('phone_number', $paymentData);
        $this->assertArrayHasKey('payment_type_code', $paymentData);
        $this->assertArrayHasKey('merchant_payment_code', $paymentData);
        $this->assertArrayHasKey('currency_code', $paymentData);
        $this->assertArrayHasKey('amount_total', $paymentData);

        $this->assertSame('request', $data['operation']);
        $this->assertSame($card->getName(), $paymentData['name']);
        $this->assertSame($card->getEmail(), $paymentData['email']);
        $this->assertSame($this->request->getDocumentNumber(), $paymentData['document']);
        $this->assertSame($address['address'], $paymentData['address']);
        $this->assertSame($address['street_number'], $paymentData['street_number']);
        $this->assertSame($card->getCity(), $paymentData['city']);
        $this->assertSame($card->getState(), $paymentData['state']);
        $this->assertSame($card->getPostcode(), $paymentData['zipcode']);
        $this->assertSame($card->getCountry(), $paymentData['country']);
        $this->assertSame($card->getPhone(), $paymentData['phone_number']);
        $this->assertSame($this->request->getPaymentMethod(), $paymentData['payment_type_code']);
        $this->assertSame($this->request->getTransactionId(), $paymentData['merchant_payment_code']);
        $this->assertSame($this->request->getCurrency(), $paymentData['currency_code']);
        $this->assertSame($this->request->getAmount(), $paymentData['amount_total']);
    }

    public function testGetDataBoletoPaymentMethod(){
        $this->request->setPaymentMethod('boleto');
        $this->request->setBoletoDueDate('2019-12-30');
        $data    = $this->request->getData();
        $paymentData    = $data['payment'];

        $this->assertSame('boleto', $paymentData['payment_type_code']);
        $this->assertArrayHasKey('due_date', $paymentData);
        $this->assertSame('30/12/2019', $paymentData['due_date']);
    }

    public function testGetDataCreditCardPaymentMethod(){
        $this->request->setPaymentMethod('creditcard');
        $data    = $this->request->getData();
        $card    = $this->request->getCard();
        $paymentData    = $data['payment'];

        $this->assertSame('creditcard', $paymentData['payment_type_code']);
        $this->assertArrayHasKey('instalments', $paymentData);
        $this->assertArrayHasKey('creditcard', $paymentData);

        $cardData    = $data['payment']['creditcard'];
        $this->assertArrayHasKey('card_name', $cardData);
        $this->assertArrayHasKey('card_number', $cardData);
        $this->assertArrayHasKey('card_due_date', $cardData);
        $this->assertArrayHasKey('card_cvv', $cardData);
        $this->assertArrayHasKey('auto_capture', $cardData);
        $this->assertSame($card->getName(), $cardData['card_name']);
        $this->assertSame($card->getNumber(), $cardData['card_number']);
        $this->assertSame($card->getExpiryMonth() . '/' . $card->getExpiryYear(), $cardData['card_due_date']);
        $this->assertSame($card->getCvv(), $cardData['card_cvv']);
        // As this is only a Authorize request. It must be false
        $this->assertFalse($cardData['auto_capture']);
    }

    public function testGetDataCreditCardPaymentCardHashMethod(){
        $this->request->setPaymentMethod('creditcard');
        $data         = $this->request->setCardReference('CREDIT_CARD_HASH');
        $data         = $this->request->getData();
        $paymentData  = $data['payment'];
        $cardData  = $data['payment']['creditcard'];

        $this->assertSame('creditcard', $paymentData['payment_type_code']);
        $this->assertArrayHasKey('instalments', $paymentData);
        $this->assertArrayHasKey('creditcard', $paymentData);
        $this->assertArrayHasKey('token', $cardData);
        $this->assertArrayNotHasKey('card_name', $cardData);
        $this->assertArrayNotHasKey('card_number', $cardData);
        $this->assertArrayNotHasKey('card_due_date', $cardData);
        $this->assertArrayNotHasKey('card_cvv', $cardData);

        // As this is only a Authorize request. It must be false
        $this->assertFalse($cardData['auto_capture']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The card parameter is required
     */
    public function testCardRequired()
    {
        $this->request->setCard(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('AuthorizeCreditCardSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('59ad5f00945fa382ab051651440826da7701533249b3a475', $response->getTransactionReference());
        $this->assertSame('ecc9be4512a', $response->getTransactionId());
        $this->assertSame('CO', $response->getPaymentStatus());
    }
}

