<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Subscribers;

use Arikaim\Core\Events\EventSubscriber;
use Arikaim\Core\Interfaces\Events\EventSubscriberInterface;
use Arikaim\Core\Db\Model;

/**
 * Execute before user is deleted
*/
class UserDeleteSubscriber extends EventSubscriber implements EventSubscriberInterface
{
    /**
     * Constructor
     */
    public function __construct() 
    {
        $this->subscribe('user.before.delete','beforeDelete');          
    }

    /**
     * Delete user shared files action
     *
     * @param EventInterface $event
     * @return void
    */
    public function beforeDelete($event)
    {
        $model = Model::create('Subscriptions','subscriptions');
        $data = $event->getParameters();    
      
        $model->deleteSubscription($data['id']);
    } 
}
