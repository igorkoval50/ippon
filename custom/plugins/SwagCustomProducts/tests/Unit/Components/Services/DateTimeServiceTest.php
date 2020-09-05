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

namespace SwagCustomProducts\Tests\Unit\Components\Services;

use SwagCustomProducts\Components\Services\DateTimeService;

class DateTimeServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DateTimeService
     */
    private $service;

    /**
     * @before
     */
    public function createServiceBefore()
    {
        $this->service = new DateTimeService();
    }

    public function test_it_can_be_created()
    {
        $dateTimeService = new DateTimeService();
        static::assertInstanceOf(DateTimeService::class, $dateTimeService);
    }

    public function test_it_should_create_date_time()
    {
        $result = $this->service->getDateTime();

        static::assertInstanceOf(\DateTime::class, $result);
    }

    public function test_it_should_format_date_to_unix_timestamp()
    {
        $result = $this->service->getNowString();

        static::assertEquals((new \DateTime())->format('U'), $result);
    }

    public function test_it_should_change_format_string_to_another_format()
    {
        $result = $this->service->changeFormatString('2017-01-01 10:10', 'Y:m:d H:s');

        static::assertEquals('2017:01:01 10:00', $result);
    }
}
