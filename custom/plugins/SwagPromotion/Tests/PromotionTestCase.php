<?php
/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace SwagPromotion\Tests;

class PromotionTestCase extends \Enlight_Components_Test_Controller_TestCase
{
    public function reset()
    {
        parent::reset();

        Shopware()->Container()->reset('swag_promotion.discount_command_handler');

        if (!Shopware()->Container()->has('swag_business_essentials.subscriber.frontend_dispatch')) {
            return;
        }

        Shopware()->Events()->addSubscriber(Shopware()->Container()->get('swag_business_essentials.subscriber.frontend_dispatch'));
    }
}
