<?php
/**
 * Ebanx createCard Request.
 */
namespace Omnipay\Ebanx\Message;

/**
 * Ebanx createCard Request.
 *
 * The create card request is used to create a token
 * for a given credit card to be used for recurrent payments.
 *
 * Example:
 *
 * <code>
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
 *   $transaction = $gateway->createCard(array(
 *       'card'             => $card,
 *   ));
 *
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo $response->getCardReference();
 *   }
 * </code>
 *
 * @see  \Omnipay\Ebanx\Gateway
 * @link https://developers.ebanxpagamentos.com/api-reference/ebanx-payment-api/payment-reference/reference-token-operation/
 */
class CreateCardRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('card');

        $data = array_merge($this->getDefaultParameters(), $this->getCardData());
        $data['country'] = $this->getCard()->getCountry();
        $data['payment_type_code'] = $this->getCard()->getBrand();

        return $data;
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/token';
    }
}
