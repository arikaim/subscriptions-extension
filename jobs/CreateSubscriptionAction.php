<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Jobs;

use Arikaim\Core\Queue\Jobs\Job;
use Arikaim\Core\Queue\Jobs\JobResult;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Interfaces\Job\JobInterface;
use Arikaim\Core\Utils\Text;

/**
* Create subscription action
*/
class CreateSubscriptionAction extends Job implements JobInterface
{
    /**
     * Create subscription api
     *
     * @Api(
     *      description="Create subscription",    
     *      parameters={
     *          @ApiParameter (name="name",type="string",required=true,description="Action name"),
     *          @ApiParameter (name="secret",type="string",required=false,description="Secret key"),
     *          @ApiParameter (name="user",type="string",required=true,description="User id, uuid or user name"),
     *          @ApiParameter (name="plan",type="string",required=true,description="Subscription plan slug"),
     *          @ApiParameter (name="expire_period",type="string",required=false,description="Expire period (P7D,P1Y,P6M)"),
     *      }
     * )
     * 
     * @ApiResponse(
     *      fields={
     *          @ApiParameter (name="uuid",type="string",description="Subscription uuid")
     *      }
     * )
     * 
     * Run signup action job
     *
     * @return mixed
     */
    public function execute()
    {      
        $result = JobResult::create();

        $plan = $this->getParam('plan');
        $user = $this->getParam('user');
        $expirePeriod = (string)$this->getParam('expire_period');
        
        if (empty($plan) == true) {
            return $result->error('Not valid subscription plan.');
        }
        if (empty($user) == true) {
            return $result->error('Not valid user.');
        }
        // check user
        $user = Model::Users()->findUser($user);
        if (\is_object($user) == false) {
            return $result->error('Not valid user.');
        }
        // check plan
        $plan = Arikaim::getService('subscriptions')->getPlan($plan);
        if (\is_object($plan) == false) {
            return $result->error('Not valid plan slug.');
        }

        $subscription = Arikaim::getService('subscriptions')->add($user->id,$plan->id,$expirePeriod);
        if (\is_object($subscription) == false) {
            return $result->error('Error adding subscription for user.');
        }
        // dispatch event   
        Arikaim::event()->dispatch('subscriptions.create',$subscription->toArray());
      
        return $result->field('uuid',$subscription->uuid);
    }
}
