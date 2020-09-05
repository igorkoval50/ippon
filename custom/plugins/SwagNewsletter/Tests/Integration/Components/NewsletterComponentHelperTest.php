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

namespace SwagNewsletter\Tests\Integration\Components;

use PHPUnit\Framework\TestCase;
use SwagNewsletter\Components\NewsletterComponentHelper;
use SwagNewsletter\Components\NewsletterHelper;
use SwagNewsletter\Tests\KernelTestCaseTrait;

class NewsletterComponentHelperTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_createNewsletterComponent_should_return_exception()
    {
        $newsletterCmpHelper = $this->getNewsletterComponentHelper();

        self::expectExceptionMessage('"template" cannot be empty');
        $newsletterCmpHelper->createNewsletterComponent([], 12);
    }

    public function test_createNewsletterComponent_should_return_empty_component()
    {
        $newsletterCmpHelper = $this->getNewsletterComponentHelper();

        $emptyComponent = $newsletterCmpHelper->createNewsletterComponent([
            'template' => 'Foo',
            'name' => 'Bar',
            'description' => 'lorem',
        ], 15);

        self::assertEquals('Bar', $emptyComponent->getName());
        self::assertEquals('lorem', $emptyComponent->getDescription());
    }

    public function test_createNewsletterComponent_should_return_html_component()
    {
        $newsletterCmpHelper = $this->getNewsletterComponentHelper();

        $htmlComponent = $newsletterCmpHelper->createNewsletterComponent([
            'template' => 'component_html',
            'name' => 'Bar',
            'description' => 'lorem',
        ], null);

        self::assertEquals('Bar', $htmlComponent->getName());
        self::assertEquals('lorem', $htmlComponent->getDescription());
        self::assertNotEmpty($htmlComponent->getFields());
    }

    public function test_save_should_save_empty_component_to_database()
    {
        $newsletterCmpHelper = $this->getNewsletterComponentHelper();

        $pluginId = 1711;
        $newsletterCmpHelper->createNewsletterComponent([
            'template' => 'Bar',
            'name' => 'TestSave',
            'description' => 'Ipsum',
        ], $pluginId);

        $newsletterCmpHelper->save();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM `s_campaigns_component`');
        self::assertArraySubset([
            'name' => 'TestSave',
            'description' => 'Ipsum',
            'pluginID' => $pluginId,
        ], end($result));
    }

    public function test_findByPluginId_should_return_new_component()
    {
        $newsletterCmpHelper = $this->getNewsletterComponentHelper();

        $pluginId = 1805;
        $newsletterCmpHelper->createNewsletterComponent([
            'template' => 'Bar3',
            'name' => 'TestSave3',
            'description' => 'Ipsum3',
        ], $pluginId);

        $newsletterCmpHelper->save();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM `s_campaigns_component`');
        self::assertArraySubset([
            'name' => 'TestSave3',
            'description' => 'Ipsum3',
            'pluginID' => $pluginId,
        ], end($result));

        $foundCmp = $newsletterCmpHelper->findByPluginId($pluginId);

        self::assertEquals('Bar3', $foundCmp[0]->getTemplate());
    }

    /**
     * @return NewsletterComponentHelper
     */
    private function getNewsletterComponentHelper()
    {
        return new NewsletterComponentHelper(
            self::getContainer()->get('models'),
            new NewsletterHelperMock(),
            self::getContainer()->get('dbal_connection')
        );
    }
}

class NewsletterHelperMock extends NewsletterHelper
{
    public function __construct()
    {
    }

    public function getNewsletterElements(array $newsletter)
    {
        return $newsletter;
    }
}
