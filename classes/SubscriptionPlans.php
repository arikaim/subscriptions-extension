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
     * @return bool
     */
    public function create($slug, $configFile, $extensionName)
    {
        $currency = Model::Currency('currency');
        if (\is_object($currency) == false) {
            return false;
        }

        // load josn config
        $config = Extension::loadJsonConfigFile($configFile,$extensionName);

        $currencyCode = $config['currency'] ?? 'USD';
        $currency = $currency->findCurrency($currencyCode);
     
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
        $model = Model::SubscriptionPlanFeatures('subscriptions');
        $features = $config['features'] ?? [];
        foreach($features as $item) {
            $key = $item['key'] ?? null;
            if (empty($key) == true) {
                continue;
            }
            if ($model->hasFeature($key,$plan->id) == false) {
                $model->create([
                    'uuid'          => Uuid::create(),
                    'title'         => $item['title'], 
                    'key'           => $key,    
                    'item_value'    => $item['item_value'] ?? null,                        
                    'plan_id'       => $plan->id               
                ]); 
            }               
        }
      
        return true;
    }
}
