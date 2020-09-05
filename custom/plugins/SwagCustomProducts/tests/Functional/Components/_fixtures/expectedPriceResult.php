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

return [
    'totalPriceSurcharges' => 10.969999999999999,
    'totalPriceOnce' => 0.0,
    'surcharges' => [
            0 => [
                    'name' => 'Option 1',
                    'price' => 1.9944400000000002,
                    'netPrice' => 1.6760000000000002,
                    'tax' => 0.32,
                    'isParent' => false,
                    'hasParent' => false,
                    'hasSurcharge' => true,
                ],
            1 => [
                    'name' => 'Option 2',
                    'price' => 0.0,
                    'netPrice' => 0.0,
                    'tax' => 0.0,
                    'isParent' => true,
                    'hasParent' => false,
                    'hasSurcharge' => false,
                ],
            2 => [
                    'name' => 'Checkbox A',
                    'price' => 1.9944400000000002,
                    'netPrice' => 1.6760000000000002,
                    'tax' => 0.32,
                    'isParent' => true,
                    'hasParent' => false,
                    'hasSurcharge' => true,
                ],
            3 => [
                    'name' => 'Checkbox-B',
                    'price' => 1.9944400000000002,
                    'netPrice' => 1.6760000000000002,
                    'tax' => 0.32,
                    'isParent' => true,
                    'hasParent' => false,
                    'hasSurcharge' => true,
                ],
            4 => [
                    'name' => 'Checkbox C',
                    'price' => 4.999999999999991,
                    'netPrice' => 4.2016806722689,
                    'tax' => 0.8,
                    'isParent' => true,
                    'hasParent' => false,
                    'hasSurcharge' => true,
                ],
        ],
    'onceprices' => [
        ],
    'basePrice' => 19.95,
    'totalUnitPrice' => 30.919999999999998,
    'total' => 30.919999999999998,
];
