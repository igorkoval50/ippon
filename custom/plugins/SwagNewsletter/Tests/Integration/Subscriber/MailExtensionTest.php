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

namespace SwagNewsletter\Tests\Integration\Subscriber;

use PHPUnit\Framework\TestCase;
use Shopware\Models\Shop\Shop;
use SwagNewsletter\Subscriber\MailExtension;
use SwagNewsletter\Tests\KernelTestCaseTrait;

class MailExtensionTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_onFilterMailCampaignsDetailSQL_should_edit_sql()
    {
        $mailExtension = $this->getMailExtensionSubscriber();

        $eventArgs = new \Enlight_Event_EventArgs();
        $eventArgs->setReturn('SELECT foo FROM bar');

        self::assertEquals(
            'SELECT foo, position FROM bar',
            $mailExtension->onFilterMailCampaignsDetailSQL($eventArgs)
        );
    }

    public function test_getMailingDetails_should_return_null_no_third_party_elements()
    {
        $mailExtension = $this->getMailExtensionSubscriber();

        $hookArgs = new \Enlight_Hook_HookArgs($this, '', [0, 0, 15]);

        self::assertNull($mailExtension->getMailingDetails($hookArgs));
    }

    public function test_getMailingDetails_should_return_third_party_elements_sorted()
    {
        $mailExtension = $this->getMailExtensionSubscriber();

        $newsletterId = 18050;

        $this->querySql(
            file_get_contents(__DIR__ . '/_fixtures/newsletter_with_third_party_elements.sql'),
            [':newsletterId' => $newsletterId, ':articleElementId' => 12050, ':bannerElementId' => 12060, ':liveShoppingCmpId' => 15050]
        );

        $hookArgs = new \Enlight_Hook_HookArgs($this, '', [$newsletterId]);
        $hookArgs->setReturn(['containers' => [['startRow' => 2]]]);

        $mailExtension->getMailingDetails($hookArgs);

        $return = $hookArgs->getReturn()['containers'];

        self::assertCount(2, $return);
        self::assertEquals(['startRow' => 2], $return[1]);
    }

    public function test_getMailingDetails_should_return_live_shopping_elements_sorted()
    {
        $this->assertLiveShopping();

        $mailExtension = $this->getMailExtensionSubscriber();

        $newsletterId = 18060;

        $this->querySql(
            file_get_contents(__DIR__ . '/_fixtures/newsletter_with_live_shopping_element.sql'),
            [':newsletterId' => $newsletterId, ':articleElementId' => 12050, ':bannerElementId' => 12060, ':liveShoppingCmpId' => 15050]
        );

        $hookArgs = new \Enlight_Hook_HookArgs($this, '', [$newsletterId]);
        $hookArgs->setReturn(['containers' => [['startRow' => 2]]]);

        $shop = self::getContainer()->get('models')->getRepository(Shop::class)->getActiveDefault();
        $shopRegistrationService = self::getContainer()->get('shopware.components.shop_registration_service');

        $shopRegistrationService->registerResources($shop);

        $mailExtension->getMailingDetails($hookArgs);
        $return = $hookArgs->getReturn()['containers'];

        self::assertTrue($return[0]['installed']);
    }

    /**
     * @param bool $useLiveShopping
     *
     * @return MailExtension
     */
    private function getMailExtensionSubscriber()
    {
        return new MailExtension(
            self::getContainer()->get('swag_newsletter.components.live_shopping_repository'),
            self::getContainer()->get('swag_newsletter.components.suggest_service'),
            self::getContainer()->get('models'),
            self::getContainer()->get('front')
        );
    }

    /**
     * Checks if liveshopping is active.
     */
    private function assertLiveShopping()
    {
        $result = self::getKernel()
            ->getContainer()
            ->get('dbal_connection')
            ->fetchAll('SELECT * FROM s_core_plugins WHERE label = "LiveShopping"');

        if (count($result) === 0 || $result[0]['active'] === 0) {
            $this->markTestSkipped('Could not test LiveShopping compatibility.');
        }
    }
}
