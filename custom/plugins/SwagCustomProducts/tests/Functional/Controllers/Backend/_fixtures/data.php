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
    'id' => 1,
    'internalName' => 'internalName',
    'displayName' => 'ExternalName',
    'mediaId' => null,
    'articles' => [],
    'options' => [
        [
            'id' => 5,
            'name' => 'Option 1',
            'values' => [],
            'prices' => [
                ['id' => 7],
            ],
        ], [
            'id' => 6,
            'name' => 'Option 2',
            'values' => [
                [
                    'id' => 3,
                    'name' => 'Checkbox A',
                    'prices' => [
                        ['id' => 8],
                    ],
                ], [
                    'id' => 4,
                    'name' => 'Checkbox-B',
                    'prices' => [
                        ['id' => 9],
                    ],
                ], [
                    'id' => 5,
                    'name' => 'Checkbox C',
                    'prices' => [
                        ['id' => 10],
                    ],
                ],
            ],
            'prices' => [
                ['id' => 11],
            ],
        ],
    ],
];
