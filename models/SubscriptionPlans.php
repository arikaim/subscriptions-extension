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
    const BILLING_TYPE_ANNUAL  = 'annual';
    const BILLING_TYPE_MONTHLY = 'monthly';

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
    protected $table = 'subscription_plans';

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
        'api_monthly_plan_id', 
        'api_annual_plan_id',
        'slug',
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
     * Delete plan
     *
     * @param mixed $id 
     * @return bool
     */
    public function deletePlan($id = null): bool
    {        
        $model = (empty($id) == true) ? $this : $this->findById($id);
        // delete features
        $model->features()->delete();
        $model->delete();

        return true;
    }

    /**
     * Get subscribe subscription url 
     *
     * @param string $billingType
     * @param bool $currentUser
     * @return string
     */
    public function getSubscribeUrl($billingType, $currentUser = false)
    {
        if ($this->isFree() == true) {
            return 'signup'; 
        }
        $path = $this->slug . '/' . $billingType;

        return ($currentUser == true) ? 'subscription/create/' . $path : 'subscription/signup/' . $path;
    }

    /**
     * Get paid pans query
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePaidPlansQuery($query)
    {
        return $query->whereNotNull('monthly_price')->where('monthly_price','!=',0);
    }

    /**
     * Set muttator for monthly_price
     *
     * @param mixed $value
     * @return void
     */
    public function setMonthlyPriceAttribute($value)
    {
        $this->attributes['monthly_price'] = (empty($value) == true) ? (float)0.00 : $value;
    }

    /**
     * Set muttator for annual_price
     *
     * @param mixed $value
     * @return void
     */
    public function setAnnualPriceAttribute($value)
    {
        $this->attributes['annual_price'] = (empty($value) == true) ? (float)0.00 : $value;
    }

    /**
     * Return true if plan is free
     *
     * @return boolean
     */
    public function isFree() 
    {
        return (empty($this->monthly_price) == true || $this->monthly_price == 0);
    }

    /**
     * Get price
     *
     * @param string $billingType
     * @return float
     */
    public function getPrice($billingType)
    {
        return ($billingType == Self::BILLING_TYPE_ANNUAL) ? $this->annual_price : $this->monthly_price;
    }

    /**
     * Get api plan id
     *
     * @param string $billingType
     * @return string|null
     */
    public function getApiPlanId($billingType)
    {
        return ($billingType == Self::BILLING_TYPE_ANNUAL) ? $this->api_annual_plan_id : $this->api_monthly_plan_id;
    }

    /**
     * Get plan Id
     *
     * @param string $planApiId
     * @param string $billingType
     * @return int|null
     */
    public function findPlanId($planApiId, $billingType)
    {
        if ($billingType == Self::BILLING_TYPE_ANNUAL) {
            $model = $this->where('api_annual_plan_id','=',$planApiId);
        } else {
            $model = $this->where('api_monthly_plan_id','=',$planApiId);
        }
        $model = $model->first();

        return (\is_object($model) == true) ? $model->id : null;
    }

    /**
     * Save api plan id
     *
     * @param string $billingType
     * @param string $planId
     * @return bool
     */
    public function updatePlanId($billingType, $planId)
    {
        $fieldName = ($billingType == Self::BILLING_TYPE_ANNUAL) ? 'api_annual_plan_id' : 'api_monthly_plan_id';
        
        return $this->update([ 
            $fieldName => $planId
        ]);
    }

    /**
     * Get price per month
     *
     * @param string $billingType
     * @return float
     */
    public function pricePerMonth($billingType)
    {
        if ($billingType == Self::BILLING_TYPE_MONTHLY) {
            return $this->monthly_price;
        }     
        $price = (float)(empty($this->annual_price) == true) ? 0 : $this->annual_price;
        if ($price == 0) {
            return $price;
        }

        return \ceil($price / 12);
    }

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
