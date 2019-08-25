<?php

namespace League\Ebanx\Test;

use Omnipay\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{

    /**
     * Helper method used by gateway test classes to generate a valid test document number
     */
    public function getValidDocumentNumber() {
        return '853.513.468-93';
    }

    /**
     * Helper method used by gateway test classes to generate a valid test credit card
     */
    public function getValidCard()
    {
        return array(
            'firstName' => 'Ana Santos',
            'lastName' => 'Araujo',
            'number' => '5555555555554444',
            'expiryMonth' => rand(1, 12),
            'expiryYear' => gmdate('Y') + rand(1, 5),
            'cvv' => rand(100, 999),
            'billingAddress1' => 'Rua E, 1040',
            'billingAddress2' => 'Bairro',
            'billingCity' => 'Maracanaú',
            'billingPostcode' => '61919-230',
            'billingState' => 'CE',
            'billingCountry' => 'BR',
            'billingPhone' => '(85) 2284-7035',
            'shippingAddress1' => 'Rua E, 1040',
            'shippingAddress2' => 'Bairro',
            'shippingCity' => 'Maracanaú',
            'shippingPostcode' => '61919-230',
            'shippingState' => 'CE',
            'shippingCountry' => 'BR',
            'shippingPhone' => '(85) 2284-7035',
        );
    }
}

