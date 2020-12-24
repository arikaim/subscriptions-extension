<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Classes;

use Arikaim\Core\Extension\Extension;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Utils\Uuid;

/**
 * Subscription Plans
*/
class SubscriptionPlans 
{
    /**
     * Create plan
     *
     * @param string $slug
     * @param string $configFile
     * @param string $extensionName
     * @return void
     */
    public function create($slug, $configFile, $extensionName)
    {
        // load josn config
        $config = Extension::loadJsonConfigFile($configFile,$extensionName);

        $currency = Model::Currency('currency',function($query) use($config) {
            $code = $config['currency'] ?? 'USD';
            return $query->findCurrency($code);
        });

        // Add user type
        Model::seed('SubscriptionPlans','subscriptions',function($seed) use($config, $slug, $currency) {
            $seed->create(['slug' => $slug],[
                'uuid'          => Uuid::create(),
                'title'         => $config['title'], 
                'description'   => $config['description'] ?? '',   
                'monthly_price' => $config['monthly_price'] ?? 0.00,   
                'annual_price'  => $config['annual_price'] ?? 0.00,            
                'status'        => 1,
                'currency_id'   => (\is_object($currency) == true) ? $currency->id : 1 
            ]); 
        });

        // find plan
        $plan = Model::SubscriptionPlans('subscriptions',function($query) use($slug) {
            return $query->findBySlug($slug);
        });

        if (\is_object($plan) == false) {
            return false;
        }

        // create plan features      
        Model::seed('SubscriptionPlanFeatures','subscriptions',function($seed) use($config, $plan) {
            $features = $config['features'] ?? [];
            foreach($features as $item) {
                $key = $item['key'] ?? null;
                if (empty($key) == false) {
                    $seed->create(['key' => $key],[
                        'uuid'          => Uuid::create(),
                        'title'         => $item['title'], 
                        'key'           => $key,                        
                        'plan_id'       => $plan->id               
                    ]); 
                }
            }
        });
    }
}
