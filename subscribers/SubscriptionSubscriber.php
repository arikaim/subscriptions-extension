<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Arcade\Subscribers;

use Arikaim\Core\Events\EventSubscriber;
use Arikaim\Core\Interfaces\Events\EventSubscriberInterface;

/**
 * Subscriptions subscriber class
 */
class SubscriptionSubscriber extends EventSubscriber implements EventSubscriberInterface
{
    /**
     * Constructor
     *
    */
    public function __construct()
    {       
        $this->subscribe('sitemap.pages');
    }
    
    /**
     * Subscriber code executed.
     *
     * @param EventInterface $event
     * @return void
     */
    public function execute($event)
    {     
        $params = $event->getParameters();
    }
}
