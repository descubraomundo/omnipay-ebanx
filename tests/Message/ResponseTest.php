<?php

namespace Omnipay\Ebanx\Message;

use League\Ebanx\Test\TestCase;

class ResponseTest extends TestCase
{

    public function createResponse($mock) {
        return new Response($this->getMockRequest(), json_decode($mock->getBody()->getContents(), true));
    }

    public function testSuccessResquest() {
        $httpResponse = $this->getMockHttpResponse('AuthorizeBoletoSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertTrue($response->isSuccessful());
    }

    public function testFailureResquest() {
        $httpResponse = $this->getMockHttpResponse('FailureRequest.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertFalse($response->isSuccessful());
    }

    public function testRedirectResquest() {
        $httpResponse = $this->getMockHttpResponse('PaymentPageSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertTrue($response->isRedirect());
    }

    public function testGetRedirectURL() {
        $httpResponse = $this->getMockHttpResponse('PaymentPageSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertEquals('https://staging.ebanx.com.br/checkout/?hash=5ae0b5d4f1883ed4b214c0277af29f1981443f59a26eef87', $response->getRedirectUrl());
    }

    public function testGetRedirectURLAsNull() {
        $httpResponse = $this->getMockHttpResponse('AuthorizeBoletoSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertNull($response->getRedirectUrl());
    }

    public function testGetTransactionReference() {
        $httpResponse = $this->getMockHttpResponse('AuthorizeBoletoSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertEquals('59ad5dd18a6d5ba0e24327c2ba92a730115a80bd58b3baa5', $response->getTransactionReference());
    }

    public function testGetTransactionId() {
        $httpResponse = $this->getMockHttpResponse('AuthorizeBoletoSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertEquals('ecc9be4512a', $response->getTransactionId());
    }

    public function testGetTransactionIdAsNull() {
        $httpResponse = $this->getMockHttpResponse('FailureRequest.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertNull($response->getTransactionId());
    }

    public function testGetMessageOnError() {
        $httpResponse = $this->getMockHttpResponse('FailureRequest.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertEquals('[BP-SA-1] Parameter integration_key not informed', $response->getMessage());
    }

    public function testGetMessageOnBoleto() {
        $httpResponse = $this->getMockHttpResponse('AuthorizeBoletoSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertEquals('34191760071302120372714245740007572710000010038', $response->getMessage());
    }

    public function testGetMessageOnCard() {
        $httpResponse = $this->getMockHttpResponse('AuthorizeCreditCardSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertEquals('[OK] Sandbox - Test credit card, transaction captured', $response->getMessage());
    }

    public function testGetMessageOnRedirectAsNull() {
        $httpResponse = $this->getMockHttpResponse('PaymentPageSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertNull($response->getMessage());
    }

    public function testGetCardRerefence() {
        $httpResponse = $this->getMockHttpResponse('CreateCardSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertEquals('805c49a8c606b4f2d53fad5aa688ec6c3aa247b83ac2146ee148e328244670b16f5cb719766b3a82e902387670958e71c323413c62df5ce975c1abf99d2049c6', $response->getCardReference());
    }

    public function testGetBoleto() {
        $httpResponse = $this->getMockHttpResponse('AuthorizeBoletoSuccess.txt');
        $response = $this->createResponse($httpResponse);
        $boletoData = $response->getBoleto();

        $this->assertArrayHasKey('boleto_url', $boletoData);
        $this->assertSame('https://staging.ebanx.com.br/print/?hash=59ad5dd18a6d5ba0e24327c2ba92a730115a80bd58b3baa5', $boletoData['boleto_url']);
        $this->assertArrayHasKey('boleto_barcode', $boletoData);
        $this->assertSame('34191760071302120372714245740007572710000010038', $boletoData['boleto_barcode']);
        $this->assertArrayHasKey('boleto_expiration_date', $boletoData);
        $this->assertSame('2017-09-08', $boletoData['boleto_expiration_date']);
    }

    public function testGetPaymentData() {
        $httpResponse = $this->getMockHttpResponse('CaptureSuccess.txt');
        $response = $this->createResponse($httpResponse);
        $transactionStatus = [
            "acquirer" => "EBANX",
            "code" => "OK",
            "description" => "Cartão de teste autorizado - aguardando captura",
            "authcode" => "12345"
        ];

        $paymentData = [
            "hash" => "5478ba283ef8674415082844ee14817376e49bb76aa9d4c7",
            "merchant_payment_code" => "126378126",
            "order_number" => null,
            "status" => "CO",
            "status_date" => "2014-11-28 16:09:19",
            "open_date" => "2014-11-28 16:08:39",
            "confirm_date" => "2014-11-28 16:09:19",
            "transfer_date" => null,
            "amount_br" => "100.00",
            "amount_ext" => "100.00",
            "amount_iof" => "0.00",
            "currency_rate" => "1.0000",
            "currency_ext" => "BRL",
            "due_date" => "2014-12-05",
            "instalments" => "1",
            "payment_type_code" => "visa",
            "transaction_status" => $transactionStatus,
            "pre_approved" => true,
            "capture_available" => false
        ];

        $this->assertEquals($paymentData, $response->getPaymentData());
        $this->assertEquals($transactionStatus, $response->getPaymentData('transaction_status'));
        $this->assertNull($response->getPaymentData('undefined_index'));
    }

    public function testGetPaymentDataOnError() {
        $httpResponse = $this->getMockHttpResponse('FailureRequest.txt');
        $response = $this->createResponse($httpResponse);
        $this->assertNull($response->getPaymentData());
    }

    public function testGetPaymentStatus() {
        $httpResponse = $this->getMockHttpResponse('CaptureSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertEquals('CO', $response->getPaymentStatus());
    }

    public function testGetTransactionStatus() {
        $httpResponse = $this->getMockHttpResponse('CaptureSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $transactionStatus = [
            "acquirer"    => "EBANX",
            "code"        => "OK",
            "description" => "Cartão de teste autorizado - aguardando captura",
            "authcode"    => "12345",
        ];

        $this->assertEquals($transactionStatus, $response->getTransactionStatus());
    }

    public function testGetRefunds() {
        $httpResponse = $this->getMockHttpResponse('RefundSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $refunds = [
            [
                "id"                   => "68682",
                "merchant_refund_code" => null,
                "status"               => "RE",
                "request_date"         => "2018-04-19 21:00:06",
                "pending_date"         => null,
                "confirm_date"         => null,
                "cancel_date"          => null,
                "amount_ext"           => "100.00",
                "description"          => "Testing notifications"
            ]
        ];

        $this->assertEquals($refunds, $response->getRefunds());
    }

    public function testGetRiskAnalysis() {
        $httpResponse = $this->getMockHttpResponse('RiskAnalysisSuccess.txt');
        $response     = $this->createResponse($httpResponse);
        $riskAnalysis = $response->getRiskAnalysis();

        $this->assertArrayHasKey('score', $riskAnalysis);
        $this->assertEquals('2', $riskAnalysis['score']);
        $this->assertArrayHasKey('fraud_indicators', $riskAnalysis);
        $this->assertEmpty($riskAnalysis['fraud_indicators']);
        $this->assertArrayHasKey('recommendation', $riskAnalysis);
        $this->assertEquals('approve', $riskAnalysis['recommendation']);
    }

}

