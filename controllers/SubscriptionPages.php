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
use Arikaim\Extensions\Subscriptions\Classes\Subscriptions;

/**
 * Subscription pages controler
*/
class SubscriptionPages extends Controller
{
    /**
     * Subscription page
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function showPlans($request, $response, $data) 
    { 
        $language = $this->getPageLanguage($data);
        $response = $this->noCacheHeaders($response);

        return $this->pageLoad($request,$response,$data,'subscriptions>subscription',$language);
    }

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
        $language = $this->getPageLanguage($data);
        $driverName = $this->get('options')->get('subscriptions.driver');
        $driver = $this->get('driver')->create($driverName);
        $billingType = $data->get('billing','monthly');
        $planSlug = $data->get('plan');
        $userUuid = $data->get('user',$this->getUserId());

        // User
        $user = Model::Users()->findById($userUuid);
        if (\is_object($user) == false) {
            $error = ['error' => 'Not valid user'];
            return $this->pageLoad($request,$response,$error,'subscriptions>subscription.error',$language,false);
        }
        // Subscription Plan
        $plan = Model::SubscriptionPlans('subscriptions')->findBySlug($planSlug);
        if (\is_object($plan) == false) {
            $data = ['error' => 'Not valid subscription plan'];
            return $this->pageLoad($request,$response,$data,'subscriptions>subscription.error',$language);
        }
        
        $planId = $plan->getApiPlanId($billingType);
        $apiResponse = Subscriptions::createSubscription($driver,$planId,$plan->title,$plan->description);
        
        var_dump($apiResponse);
        exit();
        
        if ($apiResponse->hasError() == true) {
            $data = [
                'error'         => $apiResponse->getError(),
                'error_details' => $apiResponse->getErrorDetails(),
            ];
            return $this->pageLoad($request,$response,$data,'subscriptions>subscription.error',$language,false);
        }
        $apiResult = $apiResponse->getResult();

        // register subscription  
        $token = $apiResult['token'] ?? null;
        $planId = $apiResult['plan_id'] ?? null;

        $planId = $plan->findPlanId($planId,$billingType);   
        $model = Model::Subscriptions('subscriptions');
        $result = $model->registerSubscription($user->id,$planId,$billingType,$token,$driverName);
       
        echo "r:$result";
        exit();

        if ($result == false) {
            $error = ['error' => 'Error register subscription.'];
            return $this->pageLoad($request,$response,$error,'subscriptions>subscription.error',$language,false);
        }

        $approvalUrl = $apiResult['response']->getApprovalLink();
        if (empty($approvalUrl) == true) {
            $error = ['error' => 'Not valid approval url.'];        
            return $this->pageLoad($request,$response,$error,'subscriptions>subscription.error',$language,false);
        }
        
        // Success redirect 
        return $this->withRedirect($response,$approvalUrl);
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
        $language = $this->getPageLanguage($data);
        $model = Model::Subscriptions('subscriptions');
        $driverName = $this->get('options')->get('subscriptions.driver');
        $driver = $this->get('driver')->create($driverName);
        $params = $request->getQueryParams();
        $token = $params['token'] ?? null;

        if (empty($token) == true) {
            $error = ['error' => 'Not valid subscription approval token'];            
            return $this->pageLoad($request,$response,$error,'subscriptions>subscription.error',$language,false);
        }

        $apiResponse = $driver->subscription()->confirm($token);
        if ($apiResponse->hasError() == true) {
            $data = [
                'error'         => $apiResponse->getError(),
                'error_details' => $apiResponse->getErrorDetails(),
            ];
            return $this->pageLoad($request,$response,$data,'subscriptions>subscription.error',$language,false);
        }
        $apiResult = $apiResponse->getResult();

        // confirm subscription
        $subscriptionId = $apiResult->getId();
        $result = $model->confirmSubscription($token,$driverName,$subscriptionId);

        if ($result == false) {
            $error = ['error' => 'Subscription activation error'];  
            return $this->pageLoad($request,$response,$error,'subscriptions>subscription.error',$language,false);
        }

        return $this->pageLoad($request,$response,$data,'subscriptions>subscription.success',$language,false);        
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
        $language = $this->getPageLanguage($data);
        $driverName = $this->get('options')->get('subscriptions.driver');
        $driver = $this->get('driver')->create($driverName);
        $params = $request->getQueryParams();
        $token = $params['token'] ?? null;

        return $this->pageLoad($request,$response,$data,'subscriptions>subscription.cancel',$language);
    }
}
