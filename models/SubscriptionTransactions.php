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

use Arikaim\Modules\Checkout\Interfaces\TransactionStorageInterface;
use Arikaim\Modules\Checkout\Interfaces\TransactionInterface;

use Arikaim\Core\Db\Traits\Uuid;
use Arikaim\Core\Db\Traits\Find;
use Arikaim\Core\Db\Traits\DateCreated;

/**
 * Subscription transactions model class
 */
class SubscriptionTransactions extends Model implements TransactionStorageInterface
{
    use Uuid,
        DateCreated,
        Find;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'subscription_transactions';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'subscription_id',
        'amount',
        'currency',
        'type',
        'checkout_driver',
        'payer_email',
        'payer_name',     
        'date_created'
    ];
    
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Save transaction
     *
     * @param TransactionInterface $transaction
     * @return boolean
     */
    public function saveTransaction(TransactionInterface $transaction): bool
    {
        $model = $this->geTransaction($transaction->getTransactionId());
        if (\is_object($model) == true) {
            return false;
        }

        $details = $transaction->getDetails();
        $info = [
            'transaction_id'  => $transaction->getTransactionId(),
            'amount'          => $transaction->getAmount(),
            'subscription_id' => $transaction->getOrderId(),
            'type'            => $transaction->getType(),
            'currency'        => $transaction->getCurrency(),
            'checkout_driver' => $transaction->getCheckoutDriver(),
            'payer_email'     => $transaction->getPayerEmail(),
            'payer_name'      => $transaction->getPayerName(),
            'details'         => \json_encode($details)          
        ];
        $model = $this->create($info);

        return \is_object($model);
    }

    /**
     * Get transaction
     *
     * @param string $id
     * @return mixed
    */
    public function getTransaction($id)
    {
        return $this->where('transaction_id','=',$id)->first();
    }
}
