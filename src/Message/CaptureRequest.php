<?php

/**
 * Ebanx Capture Request.
 */

namespace Omnipay\Ebanx\Message;

/**
 * Ebanx Capture Request.
 *
 * To capture a payment, you must call the API method capture.
 * This method applies only to authorized credit card payments where auto_capture was set to false.
 *
 * You can either use the 'transactionReference' or the 'transactionId' to capture the payment:
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
 *   $transaction = $gateway->capture(array(
 *     'transactionReference' => '5d5eb3389d1bce23d265b8d2376688b09a0fafc56d453252',
 *     // 'transactionId' => '28937128947231897',
 *   ));
 *
 *   $response = $transaction->send();
 *   if ($response->isRedirect()) {
 *       // redirect to offsite payment gateway
 *       $response->redirect();
 *   } elseif ($response->isSuccessful()) {
 *       echo "Capture transaction was successful!\n";
 *       echo "Transaction reference = " . $response->getTransactionReference() . "\n";
 *   } else {
 *       // payment failed: display message to customer
 *       exit($response->getMessage());
 *   }
 * </code>
 *
 * @see  \Omnipay\Ebanx\Gateway
 * @link https://developers.ebanxpagamentos.com/api-reference/ebanx-payment-api/ebanx-payment-guide/guide-capture-a-payment/
 */
class CaptureRequest extends AbstractRequest
{
    public function getData()
    {
        /*
         * As the capture request the params should be passed in the query
         * not in the body, we return an empty array for the body, and
         * change the params at the getEndpoint method.
         */
        return [];
    }

    protected function getEndpoint()
    {
        $data                          = $this->getDefaultParameters();
        $data['hash']                  = $this->getTransactionReference();
        $data['merchant_payment_code'] = $this->getTransactionId();

        // Remove empty values to only send hash or merchant payment code
        $data = array_filter($data);

        return parent::getEndpoint() . '/capture?' . http_build_query($data, '', '&');
    }
}
