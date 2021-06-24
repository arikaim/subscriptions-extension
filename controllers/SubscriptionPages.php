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
use Arikaim\Core\View\Html\Page;
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
        $response = $this->noCacheHeaders($response);

        return $this->pageLoad($request,$response,$data,'subscriptions>subscription');
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
            return $this->subscriptionError($request,$response,'Not valid user',$language);  
        }
        // Subscription Plan
        $plan = Model::SubscriptionPlans('subscriptions')->findBySlug($planSlug);
        if (\is_object($plan) == false) {
            return $this->subscriptionError($request,$response,'Not valid subscription plan',$language);          
        }
        
        $planId = $plan->getApiPlanId($billingType);
        $apiResponse = Subscriptions::createSubscription($driver,$planId,$plan->title,$plan->description);
    
        if ($apiResponse->hasError() == true) {
            $data = [
                'error'         => $apiResponse->getError(),
                'error_details' => $apiResponse->getErrorDetails(),
            ];
            return $this->pageLoad($request,$response,$data,'subscriptions>subscription.error');
        }
        $apiResult = $apiResponse->getResult();

        // register subscription  
        $token = $apiResult['token'] ?? null;
        $planId = $apiResult['plan_id'] ?? null;

        $planId = $plan->findPlanId($planId,$billingType);   
        $model = Model::Subscriptions('subscriptions');
        $result = $model->registerSubscription($user->id,$planId,$billingType,$token,$driverName);
       
        if ($result == false) {           
            return $this->subscriptionError($request,$response,'Error register subscription.',$language);
        }

        $approvalUrl = $apiResult['response']->getApprovalLink();
        if (empty($approvalUrl) == true) {              
            return $this->subscriptionError($request,$response,'Not valid approval url.',$language);
        }
        
        // Success redirect 
        return $this->withRedirect($response,$approvalUrl);
    }

    /**
     * Show subscription error page or redirect to url 
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string $error
     * @return string $language
    */
    public function subscriptionError($request, $response, $error, $language)
    {
        $redirects = $this->get('options')->get('subscriptions.redirects');
        $url = $redirects['error_url'] ?? null;
        if (empty($url) == true) {
            return $this->withRedirect($response,$url);
        }

        return $this->pageLoad($request,$response,['error' => $error],'subscriptions>subscription.error',$language);
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
            return $this->pageLoad($request,$response,$error,'subscriptions>subscription.error',$language);
        }

        $apiResponse = $driver->subscription()->confirm($token);
        if ($apiResponse->hasError() == true) {
            $data = [
                'error'         => $apiResponse->getError(),
                'error_details' => $apiResponse->getErrorDetails(),
            ];
            return $this->pageLoad($request,$response,$data,'subscriptions>subscription.error',$language);
        }
        // confirm subscription
        $subscriptionId = $apiResponse->getResultItem('id');       
        $nextBillingDate = $apiResponse->getResultItem('next_billing_date');     
       
        $result = $model->confirmSubscription($token,$driverName,$subscriptionId,$nextBillingDate);

        if ($result == false) {
            $error = ['error' => 'Subscription activation error'];  
            return $this->pageLoad($request,$response,$error,'subscriptions>subscription.error',$language);
        }

        // event dispatch
        $this->get('event')->dispatch('subscriptions.create',$model->toArray()); 

        $redirects = $this->get('options')->get('subscriptions.redirects');
        $successUrl = $redirects['success_url'] ?? '';
        if (empty($successUrl) == false) {
            return $this->withRedirect($response,Page::getUrl($successUrl,true));
        }

        // load success page
        return $this->pageLoad($request,$response,$data,'subscriptions>subscription.success',$language);        
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
        $redirects = $this->get('options')->get('subscriptions.redirects');
        $cancelUrl = $redirects['cancel_url'] ?? '';
        if (empty($cancelUrl) == false) {
            return $this->withRedirect($response,Page::getUrl($cancelUrl,true));
        }

        return $this->pageLoad($request,$response,$data,'subscriptions>subscription.cancel',$language);
    }

    /**
     * User signup with subscription 
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function signup($request, $response, $data) 
    {
        $language = $this->getPageLanguage($data);
        $plan = $data->get('plan');
        $billing = $data->get('billing');
        $data['redirect_url'] = '/subscription/create/' . $plan . '/' . $billing . '/{{user}}';

        return $this->pageLoad($request,$response,$data,'users>user.signup',$language);
    }
}
