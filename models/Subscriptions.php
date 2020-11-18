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

use Arikaim\Modules\Checkout\Interfaces\TransactionInterface;

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
    protected $table = "subscriptions";

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'product_id',
        'price',
        'billing_type',
        'currency',
        'checkout_driver',        
        'user_id',
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
     * Create subscription row
     *
     * @param integer $userId
     * @param integer $productId
     * @param string $billingType
     * @param TransactionInterface $transaction
     * @return Model|boolean
     */
    public function saveSubscription($userId, $productId, $billingType, TransactionInterface $transaction)
    {        
        $data = [
            'transaction_id'  => $transaction->getTransactionId(),
            'billing_type'    => $billingType,
            'user_id'         => $userId,
            'priduct_id'      => $productId,
            'price'           => $transaction->getAmount(),
            'checkout_driver' => $transaction->geetCheckoutDriver(),
            'currency'        => $transaction->getCurrency(),
            'date_created'    => $transaction->getDateTimeCreated()
        ];

        return ($this->hasSubscription($userId) == true) ? $this->update($data) : $this->create($data);      
    }

    /**
     * Return true if user have subscription
     *
     * @param integer $userId
     * @return boolean
     */
    public function hasSubscription($userId)
    {
        return \is_object($this->findById($userId));
    }
}
