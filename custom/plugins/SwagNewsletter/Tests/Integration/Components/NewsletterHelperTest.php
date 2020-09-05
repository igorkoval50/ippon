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
use Shopware\Models\Newsletter\Newsletter;
use SwagNewsletter\Components\NewsletterHelper;
use SwagNewsletter\Models\Component;
use SwagNewsletter\Tests\KernelTestCaseTrait;

class NewsletterHelperTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_getNewsletterElements_should_return_third_party_elements()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 13010;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/newsletter_with_third_party_elements.sql'),
            [
                ':newsletterId' => $newsletterId,
                ':articleElementId' => 16000,
                ':bannerElementId' => 1205,
                ':liveShoppingCmpId' => 12000,
            ]
        );

        $newsletterData = $this->getNewsletterById($newsletterId);

        $newsletterWithElements = $newsletterHelper->getNewsletterElements($newsletterData);

        self::assertCount(3, $newsletterWithElements['elements']);
        self::assertEquals('liveshopping', $newsletterWithElements['elements'][0]['component'][0]['template']);
    }

    public function test_saveThirdPartyElements_should_save_third_party_element()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16010;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/newsletter_with_third_party_elements_without_fields.sql'),
            [
                ':newsletterId' => $newsletterId,
                ':articleElementId' => 16000,
                ':bannerElementId' => 1205,
                ':liveShoppingCmpId' => 12000,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        $newsletterHelper->saveThirdPartyElements($newsletter, include __DIR__ . '/_fixtures/third_party_elements.php');
        self::getContainer()->get('models')->flush();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_plugin_swag_newsletter_element_value');

        self::assertNotEmpty($result);
    }

    public function test_saveThirdPartyElements_should_skip_default_elements()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16020;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/newsletter_with_third_party_elements_without_fields.sql'),
            [
                ':newsletterId' => $newsletterId,
                ':articleElementId' => 16000,
                ':bannerElementId' => 1205,
                ':liveShoppingCmpId' => 12000,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        $newsletterHelper->saveThirdPartyElements($newsletter, include __DIR__ . '/_fixtures/third_party_elements_with_default.php');
        self::getContainer()->get('models')->flush();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_plugin_swag_newsletter_element_value');

        self::assertCount(1, $result);
    }

    public function test_saveThirdPartyElements_should_encode_json_values()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16030;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/newsletter_with_third_party_elements_without_fields.sql'),
            [
                ':newsletterId' => $newsletterId,
                ':articleElementId' => 16000,
                ':bannerElementId' => 1205,
                ':liveShoppingCmpId' => 12000,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        $newsletterHelper->saveThirdPartyElements($newsletter, include __DIR__ . '/_fixtures/third_party_json_element.php');
        self::getContainer()->get('models')->flush();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_plugin_swag_newsletter_element_value');

        self::assertEquals('{"foo":"bar"}', $result[0]['value']);
    }

    public function test_saveNewsletterElements_should_save_text_element()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16040;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/basic_newsletter.sql'),
            [
                ':newsletterId' => $newsletterId,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        $newsletterHelper->saveNewsletterElements($newsletter, include __DIR__ . '/_fixtures/text_element.php');
        self::getContainer()->get('models')->flush();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_campaigns_html');

        self::assertNotEmpty($result);
        self::assertEquals('Example headline', $result[0]['headline']);
        self::assertEquals('http://www.example.url', $result[0]['link']);
    }

    public function test_saveNewsletterElements_should_save_voucher_element()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16040;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/basic_newsletter.sql'),
            [
                ':newsletterId' => $newsletterId,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        $newsletterHelper->saveNewsletterElements($newsletter, include __DIR__ . '/_fixtures/voucher_element.php');
        self::getContainer()->get('models')->flush();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_campaigns_containers');

        self::assertNotEmpty($result);
        self::assertEquals('ctVoucher', $result[0]['type']);
        self::assertEquals('absolut', $result[0]['value']);
    }

    public function test_saveNewsletterElements_should_save_banner_element()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16040;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/basic_newsletter.sql'),
            [
                ':newsletterId' => $newsletterId,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        $newsletterHelper->saveNewsletterElements($newsletter, include __DIR__ . '/_fixtures/banner_element.php');
        self::getContainer()->get('models')->flush();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_campaigns_banner');

        self::assertNotEmpty($result);
        self::assertEquals('Example target', $result[0]['linkTarget']);
    }

    public function test_saveNewsletterElements_should_save_link_element()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16040;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/basic_newsletter.sql'),
            [
                ':newsletterId' => $newsletterId,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        $newsletterHelper->saveNewsletterElements($newsletter, include __DIR__ . '/_fixtures/link_element.php');
        self::getContainer()->get('models')->flush();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_campaigns_links');

        self::assertCount(2, $result);
        self::assertEquals('http://www.link.one', $result[0]['link']);
        self::assertEquals('http://www.link.two', $result[1]['link']);
    }

    public function test_saveNewsletterElements_should_save_suggest_element()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16040;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/basic_newsletter.sql'),
            [
                ':newsletterId' => $newsletterId,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        $newsletterHelper->saveNewsletterElements($newsletter, include __DIR__ . '/_fixtures/suggest_element.php');
        self::getContainer()->get('models')->flush();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_campaigns_containers');

        self::assertNotEmpty($result);
        self::assertEquals('ctSuggest', $result[0]['type']);
        self::assertEquals('SW10010', $result[0]['value']);
    }

    public function test_saveNewsletterElements_should_save_fix_article_element()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16040;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/basic_newsletter.sql'),
            [
                ':newsletterId' => $newsletterId,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        $newsletterHelper->saveNewsletterElements($newsletter, include __DIR__ . '/_fixtures/article_fix_element.php');
        self::getContainer()->get('models')->flush();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_campaigns_articles');

        self::assertNotEmpty($result);
        self::assertEquals('SW10178', $result[0]['articleordernumber']);
        self::assertEquals('fix', $result[0]['type']);
    }

    public function test_saveNewsletterElements_should_not_save_fix_article_element_invalid_number()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16040;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/basic_newsletter.sql'),
            [
                ':newsletterId' => $newsletterId,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        self::expectExceptionMessage("Product by ordernumber 'SW00101' not found");
        $newsletterHelper->saveNewsletterElements($newsletter, include __DIR__ . '/_fixtures/invalid_article_fix_element.php');
    }

    public function test_saveNewsletterElements_should_not_save_article_element_no_data()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16040;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/basic_newsletter.sql'),
            [
                ':newsletterId' => $newsletterId,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        self::expectExceptionMessage('No products set for the product element');
        $newsletterHelper->saveNewsletterElements($newsletter, include __DIR__ . '/_fixtures/invalid_article_element.php');
    }

    public function test_saveNewsletterElements_should_save_other_article_elements()
    {
        $newsletterHelper = $this->getNewsletterHelper();

        $newsletterId = 16040;

        self::querySql(
            file_get_contents(__DIR__ . '/_fixtures/basic_newsletter.sql'),
            [
                ':newsletterId' => $newsletterId,
            ]
        );

        $newsletter = self::getContainer()->get('models')->getRepository(Newsletter::class)->find($newsletterId);

        $newsletterHelper->saveNewsletterElements($newsletter, include __DIR__ . '/_fixtures/other_article_elements.php'
        );
        self::getContainer()->get('models')->flush();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_campaigns_articles');

        self::assertCount(3, $result);
        self::assertEquals('random', $result[0]['type']);
        self::assertEquals('top', $result[1]['type']);
        self::assertEquals('new', $result[2]['type']);
    }

    /**
     * @param string $id
     *
     * @return array
     */
    private function getNewsletterById($id)
    {
        /** @var \SwagNewsletter\Models\Repository $newsletterRepository */
        $newsletterRepository = Shopware()->Models()->getRepository(Component::class);

        return $newsletterRepository->getNewsletterDetailQuery($id)->getArrayResult()[0];
    }

    /**
     * @return NewsletterHelper
     */
    private function getNewsletterHelper()
    {
        return new NewsletterHelper(
            self::getContainer()->get('models'),
            self::getContainer()->get('shopware_media.media_service')
        );
    }
}
