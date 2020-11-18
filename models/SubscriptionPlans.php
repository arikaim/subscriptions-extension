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
use Arikaim\Extensions\Currency\Models\Currency;
use Arikaim\Extensions\Subscriptions\Models\SubscriptionPlanFeatures;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\Status;
use Arikaim\Core\Db\Traits\Slug;
use Arikaim\Core\Db\Traits\DateCreated;

/**
 * Subscription plans model class
 */
class SubscriptionPlans extends Model 
{
    use Uuid,
        Slug,     
        Status,
        DateCreated,    
        Find;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = "subscription_plans";

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'monthly_price',
        'annual_price',
        'currency_id',
        'status',        
        'slug',
        'date_created'      
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Plan features relations
     *
     * @return Relation
     */
    public function features()
    {
        return $this->hasMany(SubscriptionPlanFeatures::class,'plan_id');
    }

    /**
     * Return true if plan feature exist
     *
     * @param string $key
     * @return boolean
     */
    public function hasFeature($key, $planId = null)
    {
        $planId = $planId ?? $this->plan_id;
    }

    /**
     * Currency relation
     *
     * @return Relation|Model
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class,'currency_id')->withDefault(function ($currency) {
            return $currency->getDefault();
        });
    }

    /**
     * Return true if plan exist
     *
     * @param string $title
     * @return boolean
     */
    public function hasPlan($title)
    {
        $model = $this->findByColumn($title,'title');
        if (\is_object($model) == true) {
            return true;
        }
        $model = $this->findBySlug($title);

        return \is_object($model);
    }
}
