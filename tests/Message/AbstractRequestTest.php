<?php

namespace Omnipay\Ebanx\Message;

use Mockery;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = Mockery::mock('\Omnipay\Ebanx\Message\AbstractRequest')->makePartial();
        $this->request->initialize();
    }

    public function testGetEndpoint()
    {
        $this->assertStringStartsWith('https://api.ebanx.com.br/ws', $this->request->getEndpoint());
    }

    public function testGetTestEndpoint()
    {
        $this->request->setTestMode(true);
        $this->assertStringStartsWith('https://staging.ebanx.com.br/ws', $this->request->getEndpoint());
    }

    public function testSetIntegrationKeyToData()
    {
        $data = array();
        $this->request->setIntegrationKey('123abc');
        $data = $this->request->getParameters();

        $this->assertArrayHasKey('integration_key', $data);
        $this->assertSame('123abc', $data['integration_key']);
    }

    public function testPersonalDocumentNumber()
    {
        // CPF
        $this->assertSame($this->request, $this->request->setDocumentNumber('370.498.440-04'));
        $this->assertSame('37049844004', $this->request->getDocumentNumber());
        // CNPJ
        $this->assertSame($this->request, $this->request->setDocumentNumber('79.047.494/0001-10'));
        $this->assertSame('79047494000110', $this->request->getDocumentNumber());
    }

    public function testPersonType()
    {
        // Default
        $this->assertSame('personal', $this->request->getPersonType());
        // Business
        $this->assertSame($this->request, $this->request->setPersonType('business'));
        $this->assertSame('business', $this->request->getPersonType());
    }

    public function testCompanyName()
    {
        $this->assertSame($this->request, $this->request->setCompanyName('Fabricas Branco Ltda'));
        $this->assertSame('Fabricas Branco Ltda', $this->request->getCompanyName());
    }

    public function testSplit()
    {
        $split = [
          [
            "recipient_code" => "your_unique_recipient_code",
            "percentage"     => 60.50,
            "liable"         => true,
            "charge_fee"     => true
          ],
          [
            "recipient_code" => "your_second_unique_recipient_code",
            "percentage"     => 39.50,
            "liable"         => false,
            "charge_fee"     => false
          ]
        ];

        $this->assertSame($this->request, $this->request->setSplit($split));
        $this->assertSame($split, $this->request->getSplit());
    }

    public function testNote()
    {
        $this->assertSame($this->request, $this->request->setNote('Payment Note'));
        $this->assertSame('Payment Note', $this->request->getNote());
    }

    public function testGetDefaultParameters()
    {
        $defaultParameters = $this->request->getDefaultParameters();
        $this->assertArrayHasKey('integration_key', $defaultParameters);
    }

    public function testGetCustomerData()
    {
        $cardData = $this->getValidCard();
        $this->request->setCard($cardData);
        $card = $this->request->getCard();
        $this->request->setDocumentNumber('370.498.440-04');

        $customerData = $this->request->getCustomerData();
        $this->assertArrayHasKey('name', $customerData);
        $this->assertSame($customerData['name'], $card->getName());
        $this->assertArrayHasKey('email', $customerData);
        $this->assertSame($customerData['email'], $card->getEmail());
        $this->assertArrayHasKey('document', $customerData);
        $this->assertSame($customerData['document'], $this->request->getDocumentNumber());
        $this->assertArrayHasKey('phone_number', $customerData);
        $this->assertSame($customerData['phone_number'], $card->getPhone());

        // Bussines Customer
        $this->request->setPersonType('business');
        $this->request->setCompanyName('Fabricas Branco Ltda');
        $businessData = $this->request->getCustomerData();
        $this->assertArrayHasKey('person_type', $businessData);
        $this->assertSame($businessData['person_type'], 'business');
        $this->assertSame($businessData['name'], 'Fabricas Branco Ltda');
        $this->assertArrayHasKey('responsible', $businessData);
        $this->assertSame($businessData['responsible']['name'], $card->getName());
    }

    public function testGetCardData()
    {
        $this->request->setCard($this->getValidCard());
        $card = $this->request->getCard();
        $this->request->setInstallments(12);
        $cardData = $this->request->getCardData();

        $this->assertArrayHasKey('instalments', $cardData);
        $this->assertSame($cardData['instalments'], $this->request->getInstallments());

        $this->assertArrayHasKey('creditcard', $cardData);
        $creditcardData = $cardData['creditcard'];
        $this->assertArrayHasKey('card_name', $creditcardData);
        $this->assertSame($cardData['creditcard']['card_name'], $card->getName());
        $this->assertArrayHasKey('card_number', $creditcardData);
        $this->assertSame($cardData['creditcard']['card_number'], $card->getNumber());
        $this->assertArrayHasKey('card_due_date', $creditcardData);
        $this->assertSame($cardData['creditcard']['card_due_date'], $card->getExpiryMonth() . '/' . $card->getExpiryYear());

        if($card->getCVV()) {
            $this->assertArrayHasKey('card_cvv', $creditcardData);
            $this->assertSame($cardData['creditcard']['card_cvv'], $card->getCVV());
        }

    }

    public function testGetCardDataWithCardReference()
    {
        $this->request->setCardReference("CARD_HASH_REFERENCE");
        $cardData = $this->request->getCardData();
        $creditcardData = $cardData['creditcard'];

        $this->assertArrayHasKey('token', $creditcardData);
        $this->assertSame("CARD_HASH_REFERENCE", $creditcardData['token']);
    }

    public function testGetCardDataWithoutCard()
    {
        $cardData = $this->request->getCardData();

        $this->assertArrayNotHasKey('creditcard', $cardData);
    }

    public function testBoletoDueDate()
    {
        $this->assertSame($this->request, $this->request->setBoletoDueDate('2019-08-23'));
        // Gateway Default Format
        $this->assertSame('23/08/2019', $this->request->getBoletoDueDate());
        // Custom Format
        $this->assertSame('Aug 23,2019', $this->request->getBoletoDueDate('M d,Y'));
    }

    public function testGetAddressValidString()
    {
        $card = $this->getValidCard();
        $card['billingAddress1'] = ' Rua Foo Bar, 12 , Complement ';
        $card['shippingAddress1'] = $card['billingAddress1'];
        $this->request->setCard($card);

        $result = $this->request->getAddressData();

        $this->assertSame('Rua Foo Bar', $result['address']);
        $this->assertSame('12', $result['street_number']);
        $this->assertSame('Complement', $result['street_complement']);
    }

    public function testExtractAddressWithInsufficientParameters()
    {
        $card = $this->getValidCard();
        $card['billingAddress1'] = ' Rua Foo Bar, 12 ';
        $card['shippingAddress1'] = $card['billingAddress1'];
        $this->request->setCard($card);

        $result = $this->request->getAddressData();

        $this->assertSame('Rua Foo Bar', $result['address']);
        $this->assertSame('12', $result['street_number']);
        $this->assertSame('', $result['street_complement']);
    }

    public function testGetSplitData()
    {
        $split = [
          [
            "recipient_code" => "your_unique_recipient_code",
            "percentage"     => 60.50,
            "liable"         => true,
            "charge_fee"     => true
          ],
          [
            "recipient_code" => "your_second_unique_recipient_code",
            "percentage"     => 39.50,
            "liable"         => false,
            "charge_fee"     => false
          ]
        ];

        $this->request->setSplit($split);
        $splitData = $this->request->getSplitData();

        $this->assertSame($split, $splitData['split']);
    }

    public function testGetSplitDataWithoutSplit()
    {
        $splitData = $this->request->getSplitData();
        $this->assertArrayNotHasKey('split', $splitData);
    }


    public function testGetPaymentData()
    {
        $this->request->setDocumentNumber('370.498.440-04');
        $this->request->setCard($this->getValidCard());
        $this->request->setTransactionId('transaction_id');
        $this->request->setCurrency('BRL');
        $this->request->setAmount('100.20');
        $this->request->setPaymentMethod('boleto');

        $paymentData = $this->request->getPaymentData();
        $this->assertArrayHasKey('payment', $paymentData);

        $subArrayPaymentData = $paymentData['payment'];

        $this->assertArrayHasKey('merchant_payment_code', $subArrayPaymentData);
        $this->assertSame($this->request->getTransactionId(), $subArrayPaymentData['merchant_payment_code']);

        $this->assertArrayHasKey('currency_code', $subArrayPaymentData);
        $this->assertSame($this->request->getCurrency(), $subArrayPaymentData['currency_code']);

        $this->assertArrayHasKey('amount_total', $subArrayPaymentData);
        $this->assertSame($this->request->getAmount(), $subArrayPaymentData['amount_total']);

        $this->assertArrayHasKey('payment_type_code', $subArrayPaymentData);
        $this->assertSame($this->request->getPaymentMethod(), $subArrayPaymentData['payment_type_code']);

    }

    public function testInstallments()
    {
        // Default
        $this->assertSame(1, $this->request->getInstallments());
        // Custom
        $this->assertSame($this->request, $this->request->setInstallments(12));
        $this->assertSame(12, $this->request->getInstallments());
    }
}