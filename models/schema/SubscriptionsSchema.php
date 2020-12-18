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
 * Subscriptions db table
 */
class SubscriptionsSchema extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'subscriptions';

    /**
     * Create table
     *
     * @param \Arikaim\Core\Db\TableBlueprint $table
     * @return void
     */
    public function create($table) 
    {
        // columns
        $table->id();
        $table->prototype('uuid');       
        $table->userId();
        $table->status();
        $table->relation('plan_id','subscription_plans');     
        $table->string('billing_type')->nullable(false);    
        $table->string('checkout_driver')->nullable(false);        
        $table->string('token')->nullable(false);        
        $table->string('subscription_id')->nullable(true);        
        $table->dateCreated();
        $table->dateUpdated();  
        // index        
        $table->unique('user_id');    
        $table->unique(['token','checkout_driver']);   
        $table->unique('subscription_id');     
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
