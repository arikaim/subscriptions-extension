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

use Arikaim\Extensions\Subscriptions\Models\SubscriptionPlans;
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
        'subscription_id',
        'date_created',
        'date_updated'
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

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
     * Confirm subscription
     *
     * @param string $token
     * @param string $checkoutDriver
     * @param string $id
     * @return bool
     */
    public function confirmSubscription($token, $checkoutDriver, $id)
    {
        $model = $this->findSubscription($token,$checkoutDriver);
        if (\is_object($model) == false) {
            return false;
        }

        return $model->update([
            'status'          => 1,
            'subscription_id' => $id           
        ]);
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
     * Get user subscription
     *
     * @param int $userId
     * @return Model|null
     */
    public function getSubscription($userId)
    {
        return $this->where('user_id','=',$userId)->first();
    }

    /**
     * Find subcription
     *
     * @param string $token
     * @param string $checkoutDriver
     * @return Model|null
     */
    public function findSubscription($token, $checkoutDriver)
    {
        return $this->where('token','=',$token)->where('checkout_driver','=',$checkoutDriver)->first();
    }
}
