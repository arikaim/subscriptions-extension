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

use Arikaim\Core\Controllers\Controller;
use Arikaim\Core\Db\Model;
use Arikaim\Modules\Checkout\SubscriptionData;

// test page: http://work.com/arikaim/subscription/paypal/basic/cb3da7c7-4b89-4a82-b717-81085eee2ed2

/**
 * Subscription pages controler
*/
class SubscriptionPages extends Controller
{
    /**
     * Create subscription page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function create($request, $response, $data) 
    { 
        $driverName = $data->get('name','paypal');
        $driver = $this->get('driver')->create($driverName);

        $userUuid = $data->get('user_uuid');
        $productSlug = $data->get('product_slug');

        // Product
        $product = Model::Products('products')->findBySlug($productSlug);
        if (\is_object($product) == false) {
            return $this->pageLoad($request,$response,$data,'checkout>subscription.error');
        }
        // User
        $user = Model::Users()->findById($userUuid);
        if (\is_object($user) == false) {
            return $this->pageLoad($request,$response,$data,'checkout>subscription.error',false);
        }
      
        $subscriptonData = [           
            'invoice_id'    => null,
            'description'   => $product->getOptionValue('description')
        ];

        $this->get('event')->dispatch('subscriptions.init',[
            'user'      => $user->toArray(),
            'product'   => $product->toArray(),
            'data'      => $subscriptonData
        ]); 

        $checkoutData = SubscriptionData::create($subscriptonData);
        $checkoutData->setUserId($user->id);
        $checkoutData->setProductId($product->id);
        
        $price = \number_format($product->getPriceValue('annual-price'),2);

        $checkoutData->addItem($product->title,$price);
        if ($checkoutData->isEmpty() == true) {
            return $this->pageLoad($request,$response,$data,'checkout>subscription.error',false);
        }
   
        $dataResult = $driver->setSubscriptionData($checkoutData);
    
        if ($driver->isSuccess($dataResult) == true) {
            $checkoutUrl = $driver->getCheckoutUrl($dataResult,null); 
           
            return $this->withRedirect($response,$checkoutUrl);
        } 
        $error = [
            'error'         => $driver->getErrorMessage($dataResult),
            'errorDetails'  => $dataResult
        ];

        // show error page      
        return $this->pageLoad($request,$response,$error,'checkout>subscription.error',false);
    }

    /**
     * Subscription success page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function success($request, $response, $data) 
    {               
        $driverName = $data->get('name','paypal');
        $driver = $this->get('driver')->create($driverName);

        $checkoutData = $data->get('data',$request->getQueryParams());
        
        $token = $driver->getToken($checkoutData);
        $payer = $driver->getPayer($checkoutData);

        $details = $driver->getCheckoutDetails($token);

        $subscriptionData = $driver->createSubscriptionData($details);
        $subscriptionData->setBullingPeriod('Month');

        $result = $driver->createRecurringProfile($subscriptionData,$token);

        if ($driver->isSuccess($result) == true) {
            //done
            $this->get('event')->dispatch('subscriptions.success',[
                'data' => $subscriptionData->toArray()
            ]);  
            
            $model = Model::Transactions('checkout');
            $model->saveTransaction($transaction);

            return $this->pageLoad($request,$response,$data,'checkout>subscription.success');
        }
        print_r($result);
        echo $token;
        echo $payer;

        print_r($details);
        exit();
       
        $error = [
            'error'         => $driver->getErrorMessage($result),
            'errorDetails'  => $result
        ];

        // show error page      
        return $this->pageLoad($request,$response,$error,'checkout>subscription.error',false);
    }

    /**
     * Subscription cancel page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function cancel($request, $response, $data) 
    {               
        $driverName = $data->get('name','paypal');
        $driver = $this->get('driver')->create($driverName);

        $checkoutData = $data->get('data',null);
        if (empty($checkoutData) == true) {
            $checkoutData = $request->getQueryParams();
        }

        $result = $driver->processCancelCheckout($checkoutData);
        $this->get('event')->dispatch('checkout.cancel',$result);  

        return $this->pageLoad($request,$response,$data,'checkout>subscription.cancel');
    }
}
