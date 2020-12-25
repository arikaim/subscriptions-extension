<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Controllers\Traits;

use Arikaim\Core\Db\Model;

/**
 * Subscriptions trait
*/
trait Subscriptions 
{
    /**
     * Return true if current user is subscribed
     *
     * @param string|null $planSlug
     * @return boolean
     */
    public function hasSubscription($planSlug = null)
    {
        $userId = $this->getUserId();
        if (empty($userId) == true) {
            return false;
        }

        return Model::Subscriptions('subscriptions')->isSusbscribed($userId,$planSlug);
    }
}
