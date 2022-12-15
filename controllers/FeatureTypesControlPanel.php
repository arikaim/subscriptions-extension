<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Controllers;

use Arikaim\Core\Controllers\ControlPanelApiController;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Controllers\Traits\Crud;

/**
 * Subscription feature types control panel api controler
*/
class FeatureTypesControlPanel extends ControlPanelApiController
{
    use Crud;

    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('subscriptions::admin.messages');
        $this->setModelClass('PlanFeatureTypes');
        $this->setExtensionName('subscriptions');
    }
}
