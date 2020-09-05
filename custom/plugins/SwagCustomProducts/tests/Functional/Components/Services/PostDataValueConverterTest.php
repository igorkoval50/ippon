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

namespace SwagCustomProducts\tests\Functional\Components\Services;

use SwagCustomProducts\Components\Services\PostDataValueConverter;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use SwagCustomProducts\tests\ServicesHelper;

class PostDataValueConverterTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_convertPostData()
    {
        $serviceHelper = new ServicesHelper(Shopware()->Container());
        $serviceHelper->registerServices();

        /** @var PostDataValueConverter $service */
        $service = Shopware()->Container()->get('custom_products.post_data_value_converter');
        $result = $service->convertPostData($this->getSamplePostData(), $this->getSampleData());

        static::assertEquals([
            2 => [
                0 => 'Text',
            ],
            3 => [
                0 => '1',
                1 => '2',
            ],
            'number' => 'SW10012',
        ], $result);
    }

    private function getSamplePostData()
    {
        return [
            'module' => 'widgets',
            'controller' => 'SwagCustomProducts',
            'action' => 'saveConfiguration',
            'custom-option-id--2' => 'Text',
            'custom-option-id--3' => '1,2',
            'templateId' => '1',
            'number' => 'SW10012',
        ];
    }

    private function getSampleData()
    {
        return [
            [
                'id' => '2',
                'template_id' => '1',
                'name' => 'text1',
                'description' => '',
                'ordernumber' => '',
                'required' => '0',
                'type' => 'textfield',
                'position' => '0',
                'default_value' => '',
                'placeholder' => '',
                'is_once_surcharge' => '0',
                'max_text_length' => null,
                'min_value' => null,
                'max_value' => null,
                'max_file_size' => '3145728',
                'min_date' => null,
                'max_date' => null,
                'max_files' => '1',
                'interval' => null,
                'could_contain_values' => '0',
                'allows_multiple_selection' => '0',
                'prices' => [
                        [
                            'id' => '2',
                            'option_id' => '2',
                            'value_id' => null,
                            'surcharge' => '8.4033613445378',
                            'tax_id' => '1',
                            'customer_group_name' => 'Shopkunden',
                            'customer_group_id' => '1',
                        ],
                    ],
                'netPrice' => 8.4033613445377995,
                'surcharge' => 9.9999999999999822,
                'tax_id' => '1',
                'tax' => 1.6000000000000001,
                'isTaxFreeDelivery' => false,
            ],
            [
                'id' => '3',
                'template_id' => '1',
                'name' => 'checkbox',
                'description' => '',
                'ordernumber' => '',
                'required' => '0',
                'type' => 'checkbox',
                'position' => '1',
                'default_value' => '',
                'placeholder' => '',
                'is_once_surcharge' => '0',
                'max_text_length' => null,
                'min_value' => null,
                'max_value' => null,
                'max_file_size' => '3145728',
                'min_date' => null,
                'max_date' => null,
                'max_files' => '1',
                'interval' => null,
                'could_contain_values' => '1',
                'allows_multiple_selection' => '0',
                'prices' => [
                        [
                            'id' => '5',
                            'option_id' => '3',
                            'value_id' => null,
                            'surcharge' => '8.4033613445378',
                            'tax_id' => '1',
                            'customer_group_name' => 'Shopkunden',
                            'customer_group_id' => '1',
                        ],
                    ],
                'netPrice' => 8.4033613445377995,
                'surcharge' => 9.9999999999999822,
                'tax_id' => '1',
                'tax' => 1.6000000000000001,
                'isTaxFreeDelivery' => false,
                'values' => [
                        [
                            'id' => '1',
                            'option_id' => '3',
                            'name' => 'value1',
                            'ordernumber' => 'value1',
                            'value' => '',
                            'is_default_value' => '0',
                            'position' => '0',
                            'is_once_surcharge' => '0',
                            'media_id' => null,
                            'seo_title' => null,
                            'prices' => [
                                    [
                                        'id' => '3',
                                        'option_id' => null,
                                        'value_id' => '1',
                                        'surcharge' => '16.806722689076',
                                        'tax_id' => '1',
                                        'customer_group_name' => 'Shopkunden',
                                        'customer_group_id' => '1',
                                    ],
                                ],
                            'netPrice' => 16.806722689076,
                            'surcharge' => 20.000000000000441,
                            'tax_id' => '1',
                            'tax' => 3.1899999999999999,
                            'isTaxFreeDelivery' => false,
                        ],
                        [
                            'id' => '2',
                            'option_id' => '3',
                            'name' => 'value2',
                            'ordernumber' => 'value2',
                            'value' => '',
                            'is_default_value' => '0',
                            'position' => '1',
                            'is_once_surcharge' => '0',
                            'media_id' => null,
                            'seo_title' => null,
                            'prices' => [
                                    [
                                        'id' => '4',
                                        'option_id' => null,
                                        'value_id' => '2',
                                        'surcharge' => '16.806722689076',
                                        'tax_id' => '1',
                                        'customer_group_name' => 'Shopkunden',
                                        'customer_group_id' => '1',
                                    ],
                                ],
                            'netPrice' => 16.806722689076,
                            'surcharge' => 20.000000000000441,
                            'tax_id' => '1',
                            'tax' => 3.1899999999999999,
                            'isTaxFreeDelivery' => false,
                        ],
                    ],
            ],
        ];
    }
}
