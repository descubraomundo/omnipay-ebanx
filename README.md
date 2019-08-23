# Omnipay: Ebanx

**Ebanx gateway for the Omnipay PHP payment processing library**

[![Build Status](https://img.shields.io/travis/descubraomundo/omnipay-ebanx/master.svg?style=flat-square)](https://travis-ci.org/descubraomundo/omnipay-ebanx) [![Code Climate](https://codeclimate.com/github/descubraomundo/omnipay-ebanx/badges/gpa.svg)](https://codeclimate.com/github/descubraomundo/omnipay-ebanx)  [![Test Coverage](https://codeclimate.com/github/descubraomundo/omnipay-ebanx/badges/coverage.svg)](https://codeclimate.com/github/descubraomundo/omnipay-ebanx/coverage)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/descubraomundo/omnipay-ebanx.svg?style=flat-square)](https://packagist.org/packages/descubraomundo/omnipay-ebanx)
[![Total Downloads](https://img.shields.io/packagist/dt/descubraomundo/omnipay-ebanx.svg?style=flat-square)](https://packagist.org/packages/descubraomundo/omnipay-ebanx)

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Pagar.Me support for Omnipay.

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what
PSRs you support to avoid any confusion with users and contributors.

## Install

Instal the gateway using require. Require the `league/omnipay` base package and this gateway.

``` bash
$ composer require league/omnipay descubraomundo/omnipay-ebanx
```

## Usage

The following gateways are provided by this package:

 * Ebanx

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay) repository.

### Example with Credit Card
``` php
// Create a gateway for the Ebanx Gateway
  // (routes to GatewayFactory::create)
  $gateway = Omnipay::create('Ebanx');

  // Initialise the gateway
  $gateway->initialize(array(
      'integration_key' => 'MyApiKey',
  ));

  // Create a credit card object
  // This card can be used for testing.
  $card = new CreditCard(array(
              'firstName'    => 'Example',
              'lastName'     => 'Customer',
              //'name'         => 'Example Customer',
              'birthday'     => '1988-02-28',
              'gender'       => 'M',
              'number'       => '4242424242424242',
              'expiryMonth'  => '01',
              'expiryYear'   => '2020',
              'cvv'          => '123',
              'email'        => 'customer@example.com',
              'address1'     => 'Street name, Street number, Complementary',
              'address2'     => 'Neighborhood',
              'postcode'     => '05443100',
              'phone'        => '19 3242 8855',
  ));

  // Do an authorize transaction on the gateway
  $transaction = $gateway->authorize(array(
      'amount'           => '10.00',
      'paymentMethod'   => 'creditcard',
      'installments'     => 5,
      'documentNumber' => '246.375.149-23', // CPF or CNPJ
      'notifyUrl'     => 'http://application.com/api/',
      'card'             => $card,
      // 'cardReference'      => 'card_k5sT...',
  ));
  $response = $transaction->send();
  if ($response->isSuccessful()) {
      echo "Authorize transaction was successful!\n";
      $sale_id = $response->getTransactionReference();
      echo "Transaction reference = " . $sale_id . "\n";
  }
```

### Example with Boleto

``` php
  // Create a gateway for the Ebanx Gateway
  // (routes to GatewayFactory::create)
  // Create array with customer data
  $customer = array(
              'firstName'    => 'Example',
              'lastName'     => 'Customer',,
              'email'        => 'customer@example.com',
              'address1'     => 'Street name, Street number, Complementary',
              'address2'     => 'Neighborhood',
              'postcode'     => '05443100',
              'phone'        => '19 3242 8855',
  ));

  // Create a credit card object
  // The card object is required by default to get all the customer information, even if you want to use boleto payment method.
  $card = new CreditCard(array(
              'firstName'    => 'Example',
              'lastName'     => 'Customer',
              //'name'         => 'Example Customer',
              'birthday'     => '1988-02-28',
              'gender'       => 'M',
              'number'       => '4242424242424242',
              'expiryMonth'  => '01',
              'expiryYear'   => '2020',
              'cvv'          => '123',
              'email'        => 'customer@example.com',
              'address1'     => 'Street name, Street number, Complementary',
              'address2'     => 'Neighborhood',
              'city'         => 'City',
              'state'        => 'sp',
              'country'      => 'BR',
              'postcode'     => '05443100',
              'phone'        => '19 3242 8855',
  ));

  // Do an authorize transaction on the gateway
  $transaction = $gateway->authorize(array(
      'amount'           => '10.00',
      'paymentMethod'   => 'boleto',
      'documentNumber' => '246.375.149-23', // CPF or CNPJ
      'notifyUrl'     => 'http://application.com/api/',
      'card'             => $card,
  ));

  $response = $transaction->send();
  if ($response->isSuccessful()) {
      echo "Authorize Boleto transaction was successful!\n";
      $sale_id = $response->getTransactionReference();
      $boleto = $response->getBoleto();
      echo "Boleto Url = " . $boleto['boleto_url'];
      echo "Boleto Barcode = " . $boleto['boleto_barcode'];
      echo "Boleto Expiration Date = " . $boleto['boleto_expiration_date'];
      echo "Transaction reference = " . $sale_id . "\n";
  }
```


## Test Mode

Ebanx accounts use two different endpoints for the sandbox-mode and live-mode using the same API key.

In case you want to use sandbox-mode just pass the testMode parameter when seting up the gateway:
``` php
 // Create a gateway for the Ebanx Gateway
  // (routes to GatewayFactory::create)
  $gateway = Omnipay::create('Ebanx');

  // Initialise the gateway
  $gateway->initialize(array(
      'testMode' => true,
      'integration_key' => 'MyApiKey',
  ));
```

Data created with sandbox-mode credentials will never hit the credit card networks
and will never cost anyone money.


## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release announcements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/descubraomundo/omnipay-ebanx/issues),
or better yet, fork the library and submit a pull request.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email geeks@descubraomundo.com instead of using the issue tracker.

## Credits

- [descubraomundo](https://github.com/descubraomundo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
