<?php
/**
 * Ebanx Cancel Request.
 */
namespace Omnipay\Ebanx\Message;

/**
 * Ebanx Cancel Request.
 *
 * To cancel a payment, you must call the API method cancel. You can cancel a payment if, only if,
 * its status is open (OP) or pending (PE). It’s important to remember that is not possible to
 * cancel confirmed payments (CO), only refunded.
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
 *   $transaction = $gateway->cancel(array(
 *     'transactionReference' => '5d5eb3389d1bce23d265b8d2376688b09a0fafc56d453252',
 *   ));
 *
 *   $response = $transaction->send();
 * </code>
 *
 * @see  \Omnipay\Ebanx\Gateway
 * @link https://developers.ebanxpagamentos.com/api-reference/ebanx-payment-api/ebanx-payment-guide/guide-cancel-a-refund/
 */
class CancelRequest extends AbstractRequest
{
    public function getData()
    {
        /*
         * As the cancel request the params should be passed in the query
         * not in the body, we return an empty array for the body, and
         * change the params at the getEndpoint method.
         */
        return [];
    }

    public function getEndpoint()
    {
        $data                          = $this->getDefaultParameters();
        $data['hash']                  = $this->getTransactionReference();

        return parent::getEndpoint() . '/cancel?' . http_build_query($data, '', '&');
    }
}
