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

namespace SwagNewsletter\Tests\Unit\Components\SwagLiveShopping;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Shopware\Models\Plugin\Plugin;
use SwagNewsletter\Components\LiveShopping\LiveShoppingWidgetCreator;
use SwagNewsletter\Components\NewsletterComponentHelper;
use SwagNewsletter\Models\Component;
use SwagNewsletter\Tests\Mocks\NewsletterComponentHelperDummy;

class LiveShoppingWidgetCreatorTest extends TestCase
{
    public static $counter = 0;

    public function test_it_can_be_created()
    {
        $liveShoppingWidgetCreator = new LiveShoppingWidgetCreator(
            new NewsletterComponentHelperDummy()
        );

        $this->assertInstanceOf(LiveShoppingWidgetCreator::class, $liveShoppingWidgetCreator);
    }

    public function test_it_should_create_newsletter_component()
    {
        $liveShoppingWidgetCreator = new LiveShoppingWidgetCreator(
            new NewsletterComponentHelperMockCounts_createNewsletterComponent_method_calls()
        );

        $liveShoppingWidgetCreator->create($this->createPlugin());

        $this->assertEquals(1, self::$counter);
    }

    public function test_it_should_save_newsletter_component()
    {
        $liveShoppingWidgetCreator = new LiveShoppingWidgetCreator(
            new NewsletterComponentHelperMockCounts_createNewsletterComponent_method_calls()
        );

        $liveShoppingWidgetCreator->create($this->createPlugin());

        $this->assertEquals(1, self::$counter);
    }

    public function test_it_should_throw_exception_if_newsletter_component_is_already_installed()
    {
        $newsletterComponentHelperMock = $this->createMock(NewsletterComponentHelper::class);
        $newsletterComponentHelperMock->method('findByPluginId')
            ->willReturn([$this->createPlugin()]);

        $liveShoppingWidgetCreator = new LiveShoppingWidgetCreator(
            $newsletterComponentHelperMock
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Widget was already created');
        $liveShoppingWidgetCreator->create($this->createPlugin());
    }

    /**
     * @before
     */
    protected function resetCounterBefore()
    {
        self::$counter = 0;
    }

    /**
     * @return Plugin
     */
    private function createPlugin()
    {
        $plugin = new Plugin();

        /** @var Plugin|ReflectionClass $reflection */
        $reflection = new ReflectionClass($plugin);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($plugin, 1);

        return $plugin;
    }
}

class NewsletterComponentHelperMockCounts_createNewsletterComponent_method_calls extends NewsletterComponentHelper
{
    public function __construct()
    {
    }

    public function createNewsletterComponent(array $options, $pluginId)
    {
        ++LiveShoppingWidgetCreatorTest::$counter;

        return new NewsletterComponentMock();
    }

    public function save()
    {
    }

    public function findByPluginId($pluginId)
    {
    }
}

class NewsletterComponentMockCounts_save_method_calls extends NewsletterComponentHelper
{
    public function __construct()
    {
    }

    public function createNewsletterComponent(array $options, $pluginId)
    {
        return new NewsletterComponentMock();
    }

    public function save()
    {
        ++LiveShoppingWidgetCreatorTest::$counter;
    }

    public function findByPluginId($pluginId)
    {
    }
}

class NewsletterComponentMock extends Component
{
    public function __construct()
    {
    }

    public function createNumberField(array $options)
    {
    }

    public function createHiddenField(array $options)
    {
    }

    public function createTextField(array $options)
    {
    }
}
