<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Controllers;

use Arikaim\Core\Controllers\ApiController;
use Arikaim\Core\Db\Model;
use Arikaim\Modules\Checkout\Transaction;

/**
 * Subscriptions api controler
*/
class SubscriptionsApi extends ApiController
{
    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('subscriptions::admin.messages');
    }

    /**
     * IPN Notify 
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function notify($request, $response, $data) 
    {          
        $saveLog = $this->get('options')->get('subscriptions.ipn.logs',false);

        $subscription = Model::Subscriptions('subscriptions');     
        $model = Model::SubscriptionTransactions('subscriptions');
        $driverName = $this->get('options')->get('subscriptions.driver');
        $driver = $this->get('driver')->create($driverName);
        $transaction = $driver->createTransaction($data->toArray());
        
        // save to db      
        if ($transaction->isValid() == true) {
            $subscriptionId = $transaction->getOrderId();
            $subscription = $subscription->getSubscription(null,$subscriptionId);
            $orderId = (\is_object($subscription) == true) ? $subscription->id : null;
            $transaction->setOrderId($orderId);
            // log
            if ($saveLog == true) {              
                $this->logInfo('Subscriptions IPN notify',$data->toArray());
                $this->logInfo('Transction details',$transaction->toArray());
            }

            $result = $model->saveTransaction($transaction);
            // update subscription status 
            switch ($transaction->getType()) {
                case Transaction::SUBSCRIPTION_EXPIRED: 
                    $subscription->setStatus(7); // EXPIRED
                    break;
                case Transaction::SUBSCRIPTION_CANCEL: 
                    $subscription->setStatus(6); // CANCELED
                    break;
            }

        } else {
            // log error
            $this->logError('IPN data error, transaction data not vlaid',$data->toArray());
        }
       
        return $response->withStatus('200');
    }
}
