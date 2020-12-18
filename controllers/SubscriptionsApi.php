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
use Arikaim\Extensions\Subscriptions\Classes\Subscriptions;

/**
 * Subscriptions api controler
*/
class SubscriptionsApi extends ApiController
{
  

    /**
     * Constructor
     * 
     * @param Container|null $container
    */
    public function __construct($container = null) 
    {
        parent::__construct($container);
        $this->setModelClass('Subscriptions');
        $this->setExtensionName('subscriptions');
    }

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

            $result = $model->saveTransaction($transaction);
            // log
           // if ($saveLog == true) {
                $this->logInfo('IPN data',$data->toArray());
           // }
        } else {
            // log error
            $this->logError('IPN data error, transaction data not vlaid',$data->toArray());
        }
       
        return $response->withStatus('200');
    }
}
