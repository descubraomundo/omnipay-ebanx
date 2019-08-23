<?php

/**
 * Ebanx Purchase Request.
 */

namespace Omnipay\Ebanx\Message;

/**
 * Ebanx Purchase Request.
 *
 * To charge a credit card or generate a boleto, you create a new charge object.
 * If your gateway is in test mode, the supplied card won't actually be charged, though
 * everything else will occur as if in live mode. (Ebanx assumes that the
 * charge would have completed successfully).
 *
 * The card object is required by default to get all the customer information,
 * even if you want to use boleto payment method.
 * If you want you can pass the cardReference paramenter
 * in case you are making a credit card payment
 *
 * Ebanx gateway supports only two types of "paymentMethod":
 *
 * * creditcard
 * * boleto
 *
 * You must provide aditional customer details to process the request at Ebanx API
 * These details is passed using the following attributes
 *
 * * documentNumber (CPF or CNPJ)
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the Ebanx Gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('Ebanx');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(array(
 *       'integration_key' => 'MyApiKey',
 *   ));

 *   // Create a credit card object
 *   // This card can be used for testing.
 *   $card = new CreditCard(array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'number'       => '4242424242424242',
 *               'expiryMonth'  => '01',
 *               'expiryYear'   => '2020',
 *               'cvv'          => '123',
 *               'email'        => 'customer@example.com',
 *               'address1'     => 'Street name, Street number, Complement',
 *               'address2'     => 'Neighborhood',
 *               'city'         => 'City',
 *               'state'        => 'sp',
 *               'country'      => 'BR',
 *               'postcode'     => '05443100',
 *               'phone'        => '19 3242 8855',
 *   ));
 *
 *   // Do an purchase transaction on the gateway
 *   $transaction = $gateway->purchase(array(
 *       'amount'           => '10.00',
 *       'documentNumber'   => '214.278.589-40',
 *       'note'             => 'test',
 *       'paymentMethod'    => 'creditcard',
 *       'card'             => $card,
 *       'currency'         => 'BRL',
 *   ));
 *
 *   $response = $transaction->send();
 *   if ($response->isRedirect()) {
 *       // redirect to offsite payment gateway
 *       $response->redirect();
 *   } elseif ($response->isSuccessful()) {
 *       echo "Authorize transaction was successful!\n";
 *       echo "Transaction reference = " . $response->getTransactionReference() . "\n";
 *   } else {
 *       // payment failed: display message to customer
 *       exit($response->getMessage());
 *   }
 * </code>
 *
 * </code>
 *
 * Because a purchase request in Ebanx looks similar to an
 * Authorize request, this class simply extends the AuthorizeRequest
 * class and over-rides the getData method setting auto_capture = true.
 *
 * @see  \Omnipay\Ebanx\Gateway
 * @link https://developers.ebanxpagamentos.com/api-reference/ebanx-payment-api/ebanx-payment-guide/guide-capture-a-payment/
 */
class PurchaseRequest extends AuthorizeRequest
{

    public function getData()
    {
        $data = parent::getData();
        $data['payment']['creditcard']['auto_capture'] = true;

        return $data;
    }
}
