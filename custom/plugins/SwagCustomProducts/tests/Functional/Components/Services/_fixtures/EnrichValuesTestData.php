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

namespace SwagCustomProducts\tests\Functional\Components\Services\_fixtures;

use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\MediaServiceInterface;

class EnrichValuesTestData
{
    public function getMedias(MediaServiceInterface $mediaService, ContextServiceInterface $contextService): array
    {
        return $mediaService->getList(
            ['755', '677', '676', '675'],
            $contextService->createShopContext(1)
        );
    }

    public function getProductPrice(): float
    {
        return 16.809999999999999;
    }

    public function getValuesPrices(): array
    {
        return [
            5 => [
                [
                    'id' => '10',
                    'option_id' => null,
                    'value_id' => '5',
                    'surcharge' => '16.798319327731',
                    'percentage' => '0',
                    'is_percentage_surcharge' => '0',
                    'tax_id' => '1',
                    'customer_group_name' => 'Shopkunden',
                    'customer_group_id' => '1',
                ],
            ],
            6 => [
                [
                    'id' => '11',
                    'option_id' => null,
                    'value_id' => '6',
                    'surcharge' => '8.4033613445378',
                    'percentage' => '0',
                    'is_percentage_surcharge' => '0',
                    'tax_id' => '1',
                    'customer_group_name' => 'Shopkunden',
                    'customer_group_id' => '1',
                ],
            ],
            25 => [
                [
                    'id' => '39',
                    'option_id' => null,
                    'value_id' => '25',
                    'surcharge' => '4.2016806722689',
                    'percentage' => '0',
                    'is_percentage_surcharge' => '0',
                    'tax_id' => '1',
                    'customer_group_name' => 'Shopkunden',
                    'customer_group_id' => '1',
                ],
            ],
            26 => [
                [
                    'id' => '40',
                    'option_id' => null,
                    'value_id' => '26',
                    'surcharge' => '4.2016806722689',
                    'percentage' => '0',
                    'is_percentage_surcharge' => '0',
                    'tax_id' => '1',
                    'customer_group_name' => 'Shopkunden',
                    'customer_group_id' => '1',
                ],
            ],
            27 => [
                [
                    'id' => '41',
                    'option_id' => null,
                    'value_id' => '27',
                    'surcharge' => '4.2016806722689',
                    'percentage' => '0',
                    'is_percentage_surcharge' => '0',
                    'tax_id' => '1',
                    'customer_group_name' => 'Shopkunden',
                    'customer_group_id' => '1',
                ],
            ],
            28 => [
                [
                    'id' => '43',
                    'option_id' => null,
                    'value_id' => '28',
                    'surcharge' => '12.605042016807',
                    'percentage' => '0',
                    'is_percentage_surcharge' => '0',
                    'tax_id' => '1',
                    'customer_group_name' => 'Shopkunden',
                    'customer_group_id' => '1',
                ],
            ],
            41 => [
                [
                    'id' => '60',
                    'option_id' => null,
                    'value_id' => '41',
                    'surcharge' => '8.4033613445378',
                    'percentage' => '0',
                    'is_percentage_surcharge' => '0',
                    'tax_id' => '1',
                    'customer_group_name' => 'Shopkunden',
                    'customer_group_id' => '1',
                ],
            ],
            42 => [
                [
                    'id' => '61',
                    'option_id' => null,
                    'value_id' => '42',
                    'surcharge' => '16.806722689076',
                    'percentage' => '0',
                    'is_percentage_surcharge' => '0',
                    'tax_id' => '1',
                    'customer_group_name' => 'Shopkunden',
                    'customer_group_id' => '1',
                ],
            ],
            43 => [
                [
                    'id' => '62',
                    'option_id' => null,
                    'value_id' => '43',
                    'surcharge' => '4.2016806722689',
                    'percentage' => '0',
                    'is_percentage_surcharge' => '0',
                    'tax_id' => '1',
                    'customer_group_name' => 'Shopkunden',
                    'customer_group_id' => '1',
                ],
            ],
        ];
    }

    public function getValues(string $basePath): array
    {
        return [
            6 => [
                [
                    'id' => '5',
                    'option_id' => '6',
                    'name' => 'Box1',
                    'ordernumber' => 'Box1',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '0',
                    'is_once_surcharge' => '0',
                    'media_id' => null,
                    'seo_title' => null,
                ], [
                    'id' => '6',
                    'option_id' => '6',
                    'name' => 'Box2',
                    'ordernumber' => 'Box2',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '1',
                    'is_once_surcharge' => '1',
                    'media_id' => null,
                    'seo_title' => null,
                ], [
                    'id' => '28',
                    'option_id' => '6',
                    'name' => 'Box3',
                    'ordernumber' => 'Box3',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '2',
                    'is_once_surcharge' => '1',
                    'media_id' => null,
                    'seo_title' => null,
                ],
            ],
            16 => [
                [
                    'id' => '25',
                    'option_id' => '16',
                    'name' => 'Radio01',
                    'ordernumber' => 'Radio01',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '0',
                    'is_once_surcharge' => '1',
                    'media_id' => null,
                    'seo_title' => null,
                ], [
                    'id' => '26',
                    'option_id' => '16',
                    'name' => 'Radio02',
                    'ordernumber' => 'Radio02',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '1',
                    'is_once_surcharge' => '1',
                    'media_id' => null,
                    'seo_title' => null,
                ], [
                    'id' => '27',
                    'option_id' => '16',
                    'name' => 'Radio3',
                    'ordernumber' => 'Radio3',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '2',
                    'is_once_surcharge' => '1',
                    'media_id' => null,
                    'seo_title' => null,
                ],
            ],
            21 => [
                [
                    'id' => '41',
                    'option_id' => '21',
                    'name' => 'a1',
                    'ordernumber' => 'a1',
                    'value' => 'http://' . $basePath . '/media/image/0a/3e/f2/sunsmile_140x140.png',
                    'is_default_value' => '1',
                    'position' => '0',
                    'is_once_surcharge' => '1',
                    'media_id' => '677',
                    'seo_title' => null,
                ], [
                    'id' => '42',
                    'option_id' => '21',
                    'name' => 'A2',
                    'ordernumber' => 'A2',
                    'value' => 'http://' . $basePath . '/media/image/37/79/7b/sonnenschirm_140x140.png',
                    'is_default_value' => '0',
                    'position' => '1',
                    'is_once_surcharge' => '1',
                    'media_id' => '676',
                    'seo_title' => null,
                ], [
                    'id' => '43',
                    'option_id' => '21',
                    'name' => 'A3',
                    'ordernumber' => 'A3',
                    'value' => 'http://' . $basePath . '/media/image/db/30/d8/vintage_140x140.png',
                    'is_default_value' => '0',
                    'position' => '2',
                    'is_once_surcharge' => '1',
                    'media_id' => '675',
                    'seo_title' => null,
                ],
            ],
        ];
    }

    public function getResult(string $basePath): array
    {
        return [
            6 => [
                [
                    'id' => '5',
                    'option_id' => '6',
                    'name' => 'Box1',
                    'ordernumber' => 'Box1',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '0',
                    'is_once_surcharge' => '0',
                    'media_id' => null,
                    'seo_title' => null,
                    'prices' => [
                        [
                            'id' => '10',
                            'option_id' => null,
                            'value_id' => '5',
                            'surcharge' => '16.798319327731',
                            'percentage' => '0',
                            'is_percentage_surcharge' => '0',
                            'tax_id' => '1',
                            'customer_group_name' => 'Shopkunden',
                            'customer_group_id' => '1',
                        ],
                    ],
                    'netPrice' => 16.798319327731,
                    'surcharge' => 19.989999999999892,
                    'tax_id' => '1',
                    'tax' => 3.19,
                    'isTaxFreeDelivery' => false,
                    'image' => [],
                ], [
                    'id' => '6',
                    'option_id' => '6',
                    'name' => 'Box2',
                    'ordernumber' => 'Box2',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '1',
                    'is_once_surcharge' => '1',
                    'media_id' => null,
                    'seo_title' => null,
                    'prices' => [
                        [
                            'id' => '11',
                            'option_id' => null,
                            'value_id' => '6',
                            'surcharge' => '8.4033613445378',
                            'percentage' => '0',
                            'is_percentage_surcharge' => '0',
                            'tax_id' => '1',
                            'customer_group_name' => 'Shopkunden',
                            'customer_group_id' => '1',
                        ],
                    ],
                    'netPrice' => 8.4033613445378,
                    'surcharge' => 9.999999999999982,
                    'tax_id' => '1',
                    'tax' => 1.6,
                    'isTaxFreeDelivery' => false,
                    'image' => [],
                ], [
                    'id' => '28',
                    'option_id' => '6',
                    'name' => 'Box3',
                    'ordernumber' => 'Box3',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '2',
                    'is_once_surcharge' => '1',
                    'media_id' => null,
                    'seo_title' => null,
                    'prices' => [
                        [
                            'id' => '43',
                            'option_id' => null,
                            'value_id' => '28',
                            'surcharge' => '12.605042016807',
                            'percentage' => '0',
                            'is_percentage_surcharge' => '0',
                            'tax_id' => '1',
                            'customer_group_name' => 'Shopkunden',
                            'customer_group_id' => '1',
                        ],
                    ],
                    'netPrice' => 12.605042016807,
                    'surcharge' => 15.00000000000033,
                    'tax_id' => '1',
                    'tax' => 2.39,
                    'isTaxFreeDelivery' => false,
                    'image' => [],
                ],
            ],
            16 => [
                [
                    'id' => '25',
                    'option_id' => '16',
                    'name' => 'Radio01',
                    'ordernumber' => 'Radio01',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '0',
                    'is_once_surcharge' => '1',
                    'media_id' => null,
                    'seo_title' => null,
                    'prices' => [
                        [
                            'id' => '39',
                            'option_id' => null,
                            'value_id' => '25',
                            'surcharge' => '4.2016806722689',
                            'percentage' => '0',
                            'is_percentage_surcharge' => '0',
                            'tax_id' => '1',
                            'customer_group_name' => 'Shopkunden',
                            'customer_group_id' => '1',
                        ],
                    ],
                    'netPrice' => 4.2016806722689,
                    'surcharge' => 4.999999999999991,
                    'tax_id' => '1',
                    'tax' => 0.8,
                    'isTaxFreeDelivery' => false,
                    'image' => [],
                ], [
                    'id' => '26',
                    'option_id' => '16',
                    'name' => 'Radio02',
                    'ordernumber' => 'Radio02',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '1',
                    'is_once_surcharge' => '1',
                    'media_id' => null,
                    'seo_title' => null,
                    'prices' => [
                        [
                            'id' => '40',
                            'option_id' => null,
                            'value_id' => '26',
                            'surcharge' => '4.2016806722689',
                            'percentage' => '0',
                            'is_percentage_surcharge' => '0',
                            'tax_id' => '1',
                            'customer_group_name' => 'Shopkunden',
                            'customer_group_id' => '1',
                        ],
                    ],
                    'netPrice' => 4.2016806722689,
                    'surcharge' => 4.999999999999991,
                    'tax_id' => '1',
                    'tax' => 0.8,
                    'isTaxFreeDelivery' => false,
                    'image' => [],
                ], [
                    'id' => '27',
                    'option_id' => '16',
                    'name' => 'Radio3',
                    'ordernumber' => 'Radio3',
                    'value' => '',
                    'is_default_value' => '0',
                    'position' => '2',
                    'is_once_surcharge' => '1',
                    'media_id' => null,
                    'seo_title' => null,
                    'prices' => [
                        [
                            'id' => '41',
                            'option_id' => null,
                            'value_id' => '27',
                            'surcharge' => '4.2016806722689',
                            'percentage' => '0',
                            'is_percentage_surcharge' => '0',
                            'tax_id' => '1',
                            'customer_group_name' => 'Shopkunden',
                            'customer_group_id' => '1',
                        ],
                    ],
                    'netPrice' => 4.2016806722689,
                    'surcharge' => 4.999999999999991,
                    'tax_id' => '1',
                    'tax' => 0.8,
                    'isTaxFreeDelivery' => false,
                    'image' => [],
                ],
            ],
            21 => [
                [
                    'id' => '41',
                    'option_id' => '21',
                    'name' => 'a1',
                    'ordernumber' => 'a1',
                    'value' => 'http://' . $basePath . '/media/image/0a/3e/f2/sunsmile_140x140.png',
                    'is_default_value' => '1',
                    'position' => '0',
                    'is_once_surcharge' => '1',
                    'media_id' => '677',
                    'seo_title' => null,
                    'prices' => [
                        [
                            'id' => '60',
                            'option_id' => null,
                            'value_id' => '41',
                            'surcharge' => '8.4033613445378',
                            'percentage' => '0',
                            'is_percentage_surcharge' => '0',
                            'tax_id' => '1',
                            'customer_group_name' => 'Shopkunden',
                            'customer_group_id' => '1',
                        ],
                    ],
                    'netPrice' => 8.4033613445378,
                    'surcharge' => 9.999999999999982,
                    'tax_id' => '1',
                    'tax' => 1.6,
                    'isTaxFreeDelivery' => false,
                    'image' => [
                        'id' => 677,
                        'name' => 'sunsmile',
                        'description' => '',
                        'preview' => null,
                        'type' => 'IMAGE',
                        'file' => 'http://' . $basePath . '/media/image/53/46/8f/sunsmile.png',
                        'extension' => 'png',
                        'thumbnails' => [
                            [
                                'source' => 'http://' . $basePath . '/media/image/16/30/09/sunsmile_200x200.png',
                                'retinaSource' => 'http://' . $basePath . '/media/image/14/96/bb/sunsmile_200x200@2x.png',
                                'maxWidth' => '200',
                                'maxHeight' => '200',
                                'attributes' => [],
                            ], [
                                'source' => 'http://' . $basePath . '/media/image/31/76/14/sunsmile_600x600.png',
                                'retinaSource' => 'http://' . $basePath . '/media/image/46/bf/62/sunsmile_600x600@2x.png',
                                'maxWidth' => '600',
                                'maxHeight' => '600',
                                'attributes' => [],
                            ], [
                                'source' => 'http://' . $basePath . '/media/image/f4/d8/03/sunsmile_1280x1280.png',
                                'retinaSource' => 'http://' . $basePath . '/media/image/eb/cc/c5/sunsmile_1280x1280@2x.png',
                                'maxWidth' => '1280',
                                'maxHeight' => '1280',
                                'attributes' => [],
                            ],
                        ],
                        'path' => 'media/image/sunsmile.png',
                        'attributes' => [],
                    ],
                ], [
                    'id' => '42',
                    'option_id' => '21',
                    'name' => 'A2',
                    'ordernumber' => 'A2',
                    'value' => 'http://' . $basePath . '/media/image/37/79/7b/sonnenschirm_140x140.png',
                    'is_default_value' => '0',
                    'position' => '1',
                    'is_once_surcharge' => '1',
                    'media_id' => '676',
                    'seo_title' => null,
                    'prices' => [
                        [
                            'id' => '61',
                            'option_id' => null,
                            'value_id' => '42',
                            'surcharge' => '16.806722689076',
                            'percentage' => '0',
                            'is_percentage_surcharge' => '0',
                            'tax_id' => '1',
                            'customer_group_name' => 'Shopkunden',
                            'customer_group_id' => '1',
                        ],
                    ],
                    'netPrice' => 16.806722689076,
                    'surcharge' => 20.00000000000044,
                    'tax_id' => '1',
                    'tax' => 3.19,
                    'isTaxFreeDelivery' => false,
                    'image' => [
                        'id' => 676,
                        'name' => 'sonnenschirm',
                        'description' => '',
                        'preview' => null,
                        'type' => 'IMAGE',
                        'file' => 'http://' . $basePath . '/media/image/a8/e7/3c/sonnenschirm.png',
                        'extension' => 'png',
                        'thumbnails' => [
                            [
                                'source' => 'http://' . $basePath . '/media/image/58/18/f9/sonnenschirm_200x200.png',
                                'retinaSource' => 'http://' . $basePath . '/media/image/c8/71/8e/sonnenschirm_200x200@2x.png',
                                'maxWidth' => '200',
                                'maxHeight' => '200',
                                'attributes' => [],
                            ], [
                                'source' => 'http://' . $basePath . '/media/image/b1/80/27/sonnenschirm_600x600.png',
                                'retinaSource' => 'http://' . $basePath . '/media/image/9f/9a/ef/sonnenschirm_600x600@2x.png',
                                'maxWidth' => '600',
                                'maxHeight' => '600',
                                'attributes' => [],
                            ], [
                                'source' => 'http://' . $basePath . '/media/image/53/6f/05/sonnenschirm_1280x1280.png',
                                'retinaSource' => 'http://' . $basePath . '/media/image/3f/75/35/sonnenschirm_1280x1280@2x.png',
                                'maxWidth' => '1280',
                                'maxHeight' => '1280',
                                'attributes' => [],
                            ],
                        ],
                        'path' => 'media/image/sonnenschirm.png',
                        'attributes' => [],
                    ],
                ], [
                    'id' => '43',
                    'option_id' => '21',
                    'name' => 'A3',
                    'ordernumber' => 'A3',
                    'value' => 'http://' . $basePath . '/media/image/db/30/d8/vintage_140x140.png',
                    'is_default_value' => '0',
                    'position' => '2',
                    'is_once_surcharge' => '1',
                    'media_id' => '675',
                    'seo_title' => null,
                    'prices' => [
                        [
                            'id' => '62',
                            'option_id' => null,
                            'value_id' => '43',
                            'surcharge' => '4.2016806722689',
                            'percentage' => '0',
                            'is_percentage_surcharge' => '0',
                            'tax_id' => '1',
                            'customer_group_name' => 'Shopkunden',
                            'customer_group_id' => '1',
                        ],
                    ],
                    'netPrice' => 4.2016806722689,
                    'surcharge' => 4.999999999999991,
                    'tax_id' => '1',
                    'tax' => 0.8,
                    'isTaxFreeDelivery' => false,
                    'image' => [
                        'id' => 675,
                        'name' => 'vintage',
                        'description' => '',
                        'preview' => null,
                        'type' => 'IMAGE',
                        'file' => 'http://' . $basePath . '/media/image/d0/ec/98/vintage.png',
                        'extension' => 'png',
                        'thumbnails' => [
                            [
                                'source' => 'http://' . $basePath . '/media/image/37/8e/ed/vintage_200x200.png',
                                'retinaSource' => 'http://' . $basePath . '/media/image/83/e1/6d/vintage_200x200@2x.png',
                                'maxWidth' => '200',
                                'maxHeight' => '200',
                                'attributes' => [],
                            ], [
                                'source' => 'http://' . $basePath . '/media/image/18/9a/03/vintage_600x600.png',
                                'retinaSource' => 'http://' . $basePath . '/media/image/f1/1b/e8/vintage_600x600@2x.png',
                                'maxWidth' => '600',
                                'maxHeight' => '600',
                                'attributes' => [],
                            ], [
                                'source' => 'http://' . $basePath . '/media/image/bf/15/1b/vintage_1280x1280.png',
                                'retinaSource' => 'http://' . $basePath . '/media/image/4f/f0/54/vintage_1280x1280@2x.png',
                                'maxWidth' => '1280',
                                'maxHeight' => '1280',
                                'attributes' => [],
                            ],
                        ],
                        'path' => 'media/image/vintage.png',
                        'attributes' => [],
                    ],
                ],
            ],
        ];
    }
}
