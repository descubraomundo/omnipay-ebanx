<?php
/**
 * Ebanx Refund Request.
 */
namespace Omnipay\Ebanx\Message;
/**
 * Ebanx Refund Request.
 *
 * To refund a payment, you must call the API method refund. One payment can be refunded if,
 * and only if, its status confirmed (CO).
 *
 * Pay attention, because open (OP) or pending (PE) payments cannot be refunded.
 *
 * The merchant requests the refund through the Dashboard or the API.
 * This refund request gets directly into EBANX refund queue, marked as Requested.
 * EBANX then acknowledges the refund and the refund is marked as Pending.
 *
 * When the customer receives the money back (in his bank account or through his credit card),
 * the refund transitions its state to Confirmed. When the refund is Requested or Pending,
 * it can be cancelled by the merchant.
 *
 * Look at these remarks about refunds in EBANX:
 *
 * Each payment can have many refunds, given that the sum of their amounts does not exceed
 * the original payment amount. Cancelled refunds do not count toward this sum.
 *
 * Only confirmed payments (CO) can be refunded.
 *
 * The currency of the refund is the same as the original payment.
 *
 * Refunds are notified when the refund is pending and an email is sent to the customer
 * asking for bank information and when the refund is confirmed.
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
 *   // Do an refund transaction on the gateway
 *   $transaction = $gateway->refund(array(
 *     'transactionReference' => '5d5eb3389d1bce23d265b8d2376688b09a0fafc56d453252',
 *     'amount'               => '10.00',
 *     'currency'             => 'BRL',
 *     'description'          => 'Description',
 *     'transactionId'        => '32432432',
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
 * @link https://developers.ebanxpagamentos.com/api-reference/ebanx-payment-api/ebanx-payment-guide/guide-refund-a-payment/
 */
class RefundRequest extends AbstractRequest
{
    public function getData()
    {
        /*
         * As the refund request the params should be passed in the query
         * not in the body, we return an empty array for the body, and
         * change the params at the getEndpoint method.
         */
        return [];
    }

    public function getEndpoint()
    {
        $this->validate('amount', 'transactionReference', 'transactionId', 'description');
        $data                          = array_merge($this->getDefaultParameters(), $this->getSplitData());
        $data['operation']             = 'request';
        $data['hash']                  = $this->getTransactionReference();
        $data['merchant_refund_code']  = $this->getTransactionId();
        $data['amount']                = $this->getAmount();
        $data['description']           = $this->getDescription();


        return parent::getEndpoint() . '/refund?' . http_build_query($data, '', '&');
    }
}
