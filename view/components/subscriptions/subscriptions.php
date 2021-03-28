<?php 
/**
 *  Component: subscriptions
 */
use Arikaim\Core\Interfaces\View\ComponentDataInterface;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Arikaim;

/**
 * Data class
 */
return new class() implements ComponentDataInterface
{
    /**
     * Get component data
     *
     * @param array $params
     * @param ContainerInterface|null $container
     * @return array
     */
    public function getData(array $params = [], $container = null): array
    {
        $plans = Model::SubscriptionPlans('subscriptions')->getActive();
        $plans = $plans->orderBy('monthly_price')->get();
        $userId = Arikaim::get('access')->getId();      
        $subscription = (empty($userId) == false) ? Model::Subscriptions('subscriptions')->getActiveSubscription($userId) : null;
                
        return [
            'plans'        => $plans,
            'upgrade'      => Arikaim::get('access')->isLogged(),
            'subscription' => $subscription           
        ];
    }
};
