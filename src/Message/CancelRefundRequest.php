<?php
/**
 * Ebanx cancelRefund Request.
 */
namespace Omnipay\Ebanx\Message;

/**
 * Ebanx cancelRefund Request.
 *
 * To cancel a refund, you must call the API method refund.
 * A refund can be cancelled if, and only if, its status is requested (RE) or pending (PE).
 *
 * If everything goes well the refund will be immediately cancelled.
 *
 * If a refund is cancelled, the customer will not receive the money and the merchant will not be charged.
 * Customers are NOT notified about the refund cancellation.
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
 *   // Do a cancelRefund transaction on the gateway
 *   $transaction = $gateway->cancelRefund(array(
 *     'transactionReference' => '5d5eb3389d1bce23d265b8d2376688b09a0fafc56d453252',
 *     'amount'               => '10.00',
 *     'currency'             => 'BRL',
 *     'description'          => 'Description',
 *     'transactionId'        => '32432432',
 *   ));
 *
 *   $response = $transaction->send();
 * </code>
 *
 * @see  \Omnipay\Ebanx\Gateway
 * @link https://developers.ebanxpagamentos.com/api-reference/ebanx-payment-api/ebanx-payment-guide/guide-cancel-a-refund/
 */
class CancelRefundRequest extends AbstractRequest
{
    public function getData()
    {
        /*
         * As the cancel refund request the params should be passed in the query
         * not in the body, we return an empty array for the body, and
         * change the params at the getEndpoint method.
         */
        return [];
    }

    public function getEndpoint()
    {
        $this->validate('amount', 'transactionReference', 'transactionId', 'description');
        $data                          = array_merge($this->getDefaultParameters(), $this->getSplitData());
        $data['operation']             = 'cancel';
        $data['hash']                  = $this->getTransactionReference();
        $data['merchant_refund_code']  = $this->getTransactionId();
        $data['amount']                = $this->getAmount();
        $data['description']           = $this->getDescription();


        return parent::getEndpoint() . '/refund?' . http_build_query($data, '', '&');
    }
}
