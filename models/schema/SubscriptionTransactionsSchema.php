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
 * Subscription transactions db table
 */
class SubscriptionTransactionsSchema extends Schema  
{    
    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = 'subscription_transactions';

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
        $table->relation('subscription_id','subscriptions');
        $table->string('transaction_id')->nullable(true);
        $table->string('type')->nullable(false);
        $table->decimal('amount',15,4)->nullable(false);
        $table->string('currency')->nullable(false);
        $table->string('checkout_driver')->nullable(false);    
        $table->string('payer_email')->nullable(true);     
        $table->string('payer_name')->nullable(true);      
        $table->text('details')->nullable(true);
        $table->dateCreated();
        // index
        $table->index('payer_email');  
        $table->index('checkout_driver');         
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
