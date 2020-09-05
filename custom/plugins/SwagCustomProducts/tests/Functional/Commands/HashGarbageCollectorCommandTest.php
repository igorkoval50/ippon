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

namespace SwagCustomProducts\Tests\Functional\Commands;

use SwagCustomProducts\tests\CommandTestCaseTrait;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class HashGarbageCollectorCommandTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;
    use CommandTestCaseTrait;

    const COMMAND = 'swag:customproducts:configurations:cleanup';

    public function test_should_delete_all_hashes_which_are_older_than_30_days()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures.sql'));

        $result = $this->runCommand(self::COMMAND);

        static::assertArraySubset(
            [
                'Successfully cleaned up all hashes.',
            ],
            $result
        );
    }
}
