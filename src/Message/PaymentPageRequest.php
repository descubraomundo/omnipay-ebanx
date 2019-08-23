<?php
/**
 * Ebanx paymentPage Request.
 */
namespace Omnipay\Ebanx\Message;
/**
 * Ebanx paymentPage Request
 *
 * A payment page is a page inside Ebanx domain where the user will be able to
 * provide the payment off-site
 * cardReference paramenter
 *
 * Ebanx gateway supports three types of "paymentMethod" for the requestPage:
 *
 * *_all: all available payment methods for the merchant account in this country.
 * *boleto: Boleto Bancário.
 * *_creditcard: Credit Card
 *
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
 *
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
 *   // Do an authorize transaction on the gateway
 *   $transaction = $gateway->paymentPage(array(
 *       'amount'           => '10.00',
 *       'currency'         => 'BRL',
 *       'paymentMethod'    => '_all',
 *       'card'             => $card,
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
 * @see  \Omnipay\Ebanx\Gateway
 * @see  \Omnipay\Ebanx\Message\CaptureRequest
 * @link https://developers.ebanxpagamentos.com/api-reference/ebanx-payment-api/ebanx-payment-guide/guide-create-a-payment/brazil/#payment-page-API
 */
class PaymentPageRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('card', 'amount', 'currency', 'transactionId');

        $data                          = $this->getDefaultParameters();
        $data['name']                  = $this->getCard()->getName();
        $data['email']                 = $this->getCard()->getEmail();
        $data['country']               = $this->getCard()->getCountry();
        $data['payment_type_code']     = $this->getPaymentMethod() ?: '_all';
        $data['merchant_payment_code'] = $this->getTransactionId();
        $data['currency_code']         = $this->getCurrency();
        $data['amount']                = $this->getAmount();

        return $data;
    }


    public function getEndpoint()
    {
        return parent::getEndpoint() . '/request';
    }
}
