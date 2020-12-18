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
        $model = Model::SubscriptionTransactions('subscriptions');
        $driverName = $this->get('options')->get('subscriptions.driver');
        $driver = $this->get('driver')->create($driverName);
        $transaction = $driver->createTransaction($data->toArray());
        
        // save to db      
        if ($transaction->isValid() == true) {
            $result = $model->saveTransaction($transaction);
            // log
            $this->logInfo('IPN DATA',$data->toArray());
        } else {
            // log error
            $this->logError('IPN data error, transaction data not vlaid',$data->toArray());
            $result = false;
        }
       
        $this->setResponse($result,'success','error');   
        
        return $this->getResponse();
    }
}
