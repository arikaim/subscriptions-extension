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
 * Subscription Plans db table
 */
class SubscriptionPlansSchema extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'subscription_plans';

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
        $table->slug();
        $table->status();        
        $table->price(0.00,'monthly_price');
        $table->price(0.00,'annual_price');     
        $table->relation('currency_id','currency');
        $table->string('title')->nullable(false);
        $table->text('description')->nullable(true);
        $table->string('api_monthly_plan_id')->nullable(true);
        $table->string('api_annual_plan_id')->nullable(true);
        $table->dateCreated();
        $table->dateUpdated();
        // index
        $table->unique(['title']);
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
