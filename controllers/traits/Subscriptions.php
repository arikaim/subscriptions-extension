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
     * @param int|null $userId
     * @return boolean
     */
    public function hasSubscription($planSlug = null, ?int $userId = null): bool
    {
        $userId = (empty($userId) == true) ? $this->getUserId() : $userId;
        if (empty($userId) == true) {
            return false;
        }

        return (bool)Model::Subscriptions('subscriptions')->isSusbscribed($userId,$planSlug);
    }
}
