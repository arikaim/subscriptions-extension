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
use Arikaim\Core\Db\Traits\OptionsAttribute;

/**
 * Subscription plan feature types model class
 */
class PlanFeatureTypes extends Model 
{
    use Uuid,
        OptionsAttribute,       
        Find;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'plan_feature_types';

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
        'date_created'      
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Find feature type
     *
     * @param string $key
     * @return Model|null
     */
    public function findFeatyreType(string $key): ?object
    {
        $model = $this->finByColumn($key,['key']);

        return ($model != null) ? $model : $this->findById($key);        
    }
}
