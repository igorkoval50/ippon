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

namespace SwagBundle\Tests\Functional\Controllers\_fixtures;

class Test_GetPriceData
{
    public function getSet1(): array
    {
        return [[
            'group-0::273::5' => 35,
            'group-3::274::14' => 89,
        ], [
            'price' => '56,70 &euro;',
            'regularPrice' => '63,00 &euro;',
            'productPrices' => [
                '\'0\'' => [
                    'price' => '45,00 &euro;',
                    'referencePrice' => [
                        'unit' => [
                            'unit' => 'l',
                            'description' => 'Liter',
                        ],
                        'minPurchase' => 1,
                        'maxPurchase' => null,
                        'purchaseUnit' => 0.5,
                        'referenceUnit' => 1.0,
                        'referencePrice' => [
                            'numeric' => '90.00',
                            'display' => '90,00 &euro;',
                        ],
                    ],
                ],
                '\'3\'' => [
                    'price' => '18,00 &euro;',
                    'referencePrice' => [
                        'unit' => [
                            'unit' => 'l',
                            'description' => 'Liter',
                        ],
                        'minPurchase' => 1,
                        'maxPurchase' => null,
                        'purchaseUnit' => 0.5,
                        'referenceUnit' => 1.0,
                        'referencePrice' => [
                            'numeric' => '36.00',
                            'display' => '36,00 &euro;',
                        ],
                    ],
                ],
            ],
        ]];
    }

    public function getSet2(): array
    {
        return [[
            'group-0::273::5' => 12,
            'group-3::274::14' => 89,
        ], [
            'price' => '70,20 &euro;',
            'regularPrice' => '78,00 &euro;',
            'productPrices' => [
                '\'0\'' => [
                    'price' => '60,00 &euro;',
                    'referencePrice' => [
                        'unit' => [
                            'unit' => 'l',
                            'description' => 'Liter',
                        ],
                        'minPurchase' => 1,
                        'maxPurchase' => null,
                        'purchaseUnit' => 0.7,
                        'referenceUnit' => 1.0,
                        'referencePrice' => [
                            'numeric' => '85.71',
                            'display' => '85,71 &euro;',
                        ],
                    ],
                ],
                '\'3\'' => [
                    'price' => '18,00 &euro;',
                    'referencePrice' => [
                        'unit' => [
                            'unit' => 'l',
                            'description' => 'Liter',
                        ],
                        'minPurchase' => 1,
                        'maxPurchase' => null,
                        'purchaseUnit' => 0.5,
                        'referenceUnit' => 1.0,
                        'referencePrice' => [
                            'numeric' => '36.00',
                            'display' => '36,00 &euro;',
                        ],
                    ],
                ],
            ],
        ]];
    }

    public function getSet3(): array
    {
        return [[
            'group-0::273::5' => 35,
            'group-3::274::14' => 88,
        ], [
            'price' => '49,50 &euro;',
            'regularPrice' => '55,00 &euro;',
            'productPrices' => [
                '\'0\'' => [
                    'price' => '45,00 &euro;',
                    'referencePrice' => [
                        'unit' => [
                            'unit' => 'l',
                            'description' => 'Liter',
                        ],
                        'minPurchase' => 1,
                        'maxPurchase' => null,
                        'purchaseUnit' => 0.5,
                        'referenceUnit' => 1.0,
                        'referencePrice' => [
                            'numeric' => '90.00',
                            'display' => '90,00 &euro;',
                        ],
                    ],
                ],
                '\'3\'' => [
                    'price' => '10,00 &euro;',
                    'referencePrice' => [
                        'unit' => [
                            'unit' => 'l',
                            'description' => 'Liter',
                        ],
                        'minPurchase' => 1,
                        'maxPurchase' => null,
                        'purchaseUnit' => 0.2,
                        'referenceUnit' => 1.0,
                        'referencePrice' => [
                            'numeric' => '50.00',
                            'display' => '50,00 &euro;',
                        ],
                    ],
                ],
            ],
        ]];
    }
}
