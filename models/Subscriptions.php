<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Models;

use Illuminate\Database\Eloquent\Model;

use Arikaim\Core\Utils\DateTime;
use Arikaim\Extensions\Subscriptions\Models\SubscriptionPlans;
use Arikaim\Extensions\Subscriptions\Models\SubscriptionTransactions;
use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\UserRelation;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\DateCreated;
use Arikaim\Core\Db\Traits\DateUpdated;

/**
 * Subscriptions model class
 */
class Subscriptions extends Model 
{
    use Uuid,
        Status,
        UserRelation,
        DateCreated,
        DateUpdated,
        Find;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'subscriptions';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'billing_type',        
        'checkout_driver',        
        'user_id',
        'plan_id',
        'status',
        'subscription_id',
        'next_billing_date',
        'date_created',
        'date_expired',
        'date_updated'
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Transactions
     *
     * @return Relation
     */
    public function transactions()
    {
        return $this->hasMany(SubscriptionTransactions::class,'subscription_id');
    }

    /**
     * Plan relation
     *
     * @return Relation
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlans::class,'plan_id');
    }

    /**
     * Set date expired
     *
     * @param string|null $period
     * @return boolean
     */
    public function setExpirePeriod(?string $period): bool
    {
        if (empty($period) == false) {
            $expireDate = DateTime::addInterval($period);
            $expireDate = ($expireDate == false) ? null : $expireDate->getTimestamp();
        }
    
        $result = $this->update([
            'date_expired' => (empty($period) == true) ? null : $expireDate
        ]);

        return ($result !== false);
    }

    /**
     * Register subscription with PENDING status
     *
     * @param int $userId
     * @param int $planId
     * @param string $billingType
     * @param string $token
     * @param string $checkoutDriver
     * @return bool
     */
    public function registerSubscription($userId, $planId, $billingType, $token, $checkoutDriver)
    {
        $model = $this->getSubscription($userId);           
        $info = [
            'user_id'         => $userId,
            'plan_id'         => $planId,
            'token'           => $token,
            'billing_type'    => $billingType,      
            'checkout_driver' => $checkoutDriver,
            'status'          => 4 // PENDING
        ];

        if (\is_object($model) == true) {
            return $model->update($info);
        }
        
        $model = $this->create($info);
           
        return \is_object($model);
    }

    /**
     * Create subscription with PENDING status
     *
     * @param int $userId
     * @param int $planId
     * @param string $billingType
     * @param string $token
     * @param string $checkoutDriver
     * @return Model|false
     */
    public function createSubscription($userId, $planId, $billingType, $token, $checkoutDriver)
    {
        $model = $this->registerSubscription($userId, $planId, $billingType, $token, $checkoutDriver);

        return (\is_object($model) == true) ? $model : false;
    }

    /**
     * Confirm subscription
     *
     * @param string $token
     * @param string $checkoutDriver
     * @param string $id
     * @param string $nextBillingDate
     * @return bool
     */
    public function confirmSubscription($token, $checkoutDriver, $id, $nextBillingDate)
    {
        $model = $this->findSubscription($token,$checkoutDriver);
        if (\is_object($model) == false) {
            return false;
        }

        return $model->update([
            'status'            => 1, // ACTIVE
            'date_created'      => DateTime::getTimestamp(),
            'subscription_id'   => $id,
            'next_billing_date' => $nextBillingDate          
        ]);
    }

    /**
     * Activate subscription (one time payment)
     *
     * @return boolean
     */
    public function activateSubscription(): bool
    {
        $dateExpired = null;

        $result = $this->update([
            'status'            => 1, // ACTIVE
            'date_created'      => DateTime::getTimestamp(),
            'date_expired'      => $dateExpired       
        ]);

        return ($result !== false);
    }

    /**
     * Update next billing date
     *
     * @param string $date
     * @return bool
     */
    public function updateNextBillingDate($date)
    {
        return $this->update(['next_billing_date' => $date]);
    }

    /**
     * Return true if user have subscription
     *
     * @param integer $userId
     * @return boolean
     */
    public function hasSubscription($userId)
    {
        return \is_object($this->getSubscription($userId));
    }

    /**
     * Delete user subscription and transactions
     *
     * @param integer $userId
     * @return boolean
     */
    public function deleteSubscription(int $userId): bool
    {
        $subscription = $this->getSubscription($userId);
        if (\is_object($subscription) == false) {
            return true;
        }

        // delete transactions
        $subscription->transactions()->delete();

        return (bool)$subscription->delete();
    }

    /**
     * Get user subscription
     *
     * @param int $userId
     * @param int|null $subscriptionId
     * @return Model|null
     */
    public function getSubscription($userId = null, ?int $subscriptionId = null): ?object
    {
        $query = (empty($userId) == false) ? $this->where('user_id','=',$userId) : $this;
      
        if (empty($subscriptionId) == false) {
            $query = $query->where('subscription_id','=',$subscriptionId);
        }
        
        return $query->first();
    }

    /**
     * Get active user subscription
     *
     * @param int $userId
     * @return Model|null
     */
    public function getActiveSubscription($userId)
    {
        if (empty($userId) == true) {
            return null;
        }
        return $this->where('user_id','=',$userId)->where('status','=',1)->first();
    } 

    /**
     * Return true if user have active subscription 
     *
     * @param int $userId
     * @param string|null $planSlug
     * @return boolean
     */
    public function isSusbscribed($userId, ?string $planSlug = null): bool
    {
        $model = $this->getActiveSubscription($userId);
        if (\is_object($model) == false) {
            return false;
        }
        if (empty($planSlug) == false) {
            return ($model->plan->slug == $planSlug);
        }

        return true;
    }

    /**
     * Find subcription
     *
     * @param string $token
     * @param string $checkoutDriver
     * @return Model|null
     */
    public function findSubscription(string $token,string $checkoutDriver)
    {
        return $this->where('token','=',$token)->where('checkout_driver','=',$checkoutDriver)->first();
    }

    /**
     * Retrun true if plan have subscriptions
     *
     * @param int $planId
     * @return boolean
     */
    public function hasSubscriptions(int $planId)
    {
        return ($this->plansQuery($planId)->count() > 0);
    }

    /**
     * Subscriptions query 
     *
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeSubscriptionsQuery($query, $userId)
    {
        return $query->where('user_id','=',$userId);
    }

    /**
     * Get subscriptions for plan
     *
     * @param Builder $query
     * @param integer $planId
     * @return Builder
     */
    public function scopePlansQuery($query, int $planId)
    {
        return $query->where('plan_id','=',$planId);
    } 
}
