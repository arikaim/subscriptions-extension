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

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\DateCreated;

/**
 * Subscription plan features model class
 */
class SubscriptionPlanFeatures extends Model 
{
    use Uuid,
        Status,
        DateCreated,    
        Find;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'subscription_plan_features';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'key',
        'item_value',
        'options',
        'plan_id',               
        'date_created'      
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Get plan features query
     *
     * @param Builder $query
     * @param int|null $planId
     * @return Builder
     */
    public function scopePlanFeaturesQuery($query, $planId = null)
    {
        $planId = $planId ?? $this->plan_id;

        return $query->where('plan_id','=',$planId);
    }

    /**
     * Get feature
     *
     * @param string $key
     * @param int|null $planId
     * @return Model|null
     */
    public function getFeature($key, $planId = null)
    {
        return $this->planFeaturesQuery($planId)->where('key','=',$key)->first();
    }

    /**
     * Return true if feature exist
     *
     * @param string $key
     * @param int|null $planId
     * @return boolean
     */
    public function hasFeature(string $key, $planId = null): bool
    {
        $model = $this->getFeature($key,$planId);

        return \is_object($model);
    }
}
