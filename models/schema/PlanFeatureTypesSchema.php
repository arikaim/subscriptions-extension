<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Models\Schema;

use Arikaim\Core\Db\Schema;

/**
 *  Plan features type db table
 */
class PlanFeatureTypesSchema extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'plan_feature_types';

    /**
     * Create table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function create($table) 
    {
        // columns
        $table->prototype('id');
        $table->prototype('uuid');
        $table->string('title')->nullable(false);
        $table->string('key')->nullable(false);
        $table->string('item_value')->nullable(true);
        $table->text('description')->nullable(true);
        $table->text('options')->nullable(true);
        // index
        $table->unique(['key']);
    }

    /**
     * Update table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function update($table) 
    {               
    }
}
