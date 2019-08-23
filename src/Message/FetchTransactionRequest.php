<?php
/**
 * Ebanx fetchTransaction Request.
 */
namespace Omnipay\Ebanx\Message;
/**
 * Ebanx fetchTransaction Request.
 *
 * To query a payment and fetch its details, you must call the API method query.
 * You can either use the 'transactionReference' or the 'transactionId' to fetch the transaction:
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
 *   // Do an authorize transaction on the gateway
 *   $transaction = $gateway->fetchTransaction(array(
 *     'transactionReference' => '5d5eb3389d1bce23d265b8d2376688b09a0fafc56d453252',
 *     // 'transactionId' => '28937128947231897',
 *   ));
 *
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo $response->getData();
 *   } else {
 *       // payment failed: display message to customer
 *       exit($response->getMessage());
 *   }
 * </code>
 *
 * @see  \Omnipay\Ebanx\Gateway
 * @link https://developers.ebanxpagamentos.com/api-reference/ebanx-payment-api/ebanx-payment-guide/guide-capture-a-payment/
 */
class FetchTransactionRequest extends AbstractRequest
{
    public function getData()
    {
        /*
         * As the query request the params should be passed in the query
         * not in the body, we return an empty array for the body, and
         * change the params at the getEndpoint method.
         */
        return [];
    }

    public function getEndpoint()
    {
        $data                          = $this->getDefaultParameters();
        $data['hash']                  = $this->getTransactionReference();
        $data['merchant_payment_code'] = $this->getTransactionId();

        // Remove empty values to only send hash or merchant payment code
        $data = array_filter($data);

        return parent::getEndpoint() . '/query?' . http_build_query($data, '', '&');
    }
}
