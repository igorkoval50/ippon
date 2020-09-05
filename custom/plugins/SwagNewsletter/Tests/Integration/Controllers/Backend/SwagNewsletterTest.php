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

namespace SwagNewsletter\Tests\Integration\Controllers\Backend;

require_once __DIR__ . '/../../../../Controllers/Backend/SwagNewsletter.php';

use PHPUnit\Framework\TestCase;
use Shopware\Components\DependencyInjection\Container;
use SwagNewsletter\Tests\KernelTestCaseTrait;

class SwagNewsletterTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_listNewsletterAction_should_return_empty_list()
    {
        $view = new \Enlight_View_Default(
            new \Enlight_Template_Manager()
        );

        $swagNewsletterCtl = new SwagNewsletterMock(
            new \Enlight_Controller_Request_RequestTestCase(),
            new \Enlight_Controller_Response_ResponseTestCase(),
            Shopware()->Container(),
            $view
        );

        $swagNewsletterCtl->listNewslettersAction();

        self::assertEquals(0, $view->getAssign()['total']);
    }

    public function test_listNewsletterAction_should_return_newsletter_list()
    {
        $view = new \Enlight_View_Default(
            new \Enlight_Template_Manager()
        );

        $swagNewsletterCtl = new SwagNewsletterMock(
            new \Enlight_Controller_Request_RequestTestCase(),
            new \Enlight_Controller_Response_ResponseTestCase(),
            Shopware()->Container(),
            $view
        );

        $this->querySql(
            file_get_contents(__DIR__ . '/_fixtures/basic_newsletter.sql'),
            [':newsletterId' => 12000]
        );

        $swagNewsletterCtl->listNewslettersAction();

        self::assertCount(1, $view->getAssign()['data']);
        self::assertEquals('FooBar Example', $view->getAssign()['data'][0]['subject']);
    }

    public function test_listNewsletterAction_should_remove_previous_previews()
    {
        $view = new \Enlight_View_Default(
            new \Enlight_Template_Manager()
        );

        $swagNewsletterCtl = new SwagNewsletterMock(
            new \Enlight_Controller_Request_RequestTestCase(),
            new \Enlight_Controller_Response_ResponseTestCase(),
            Shopware()->Container(),
            $view
        );

        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/newsletter_preview.sql'));

        $swagNewsletterCtl->listNewslettersAction();

        $result = self::getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_campaigns_mailings');

        self::assertEmpty($result);
    }
}

class SwagNewsletterMock extends \Shopware_Controllers_Backend_SwagNewsletter
{
    /**
     * @param \Enlight_Controller_Request_RequestTestCase   $request
     * @param \Enlight_Controller_Response_ResponseTestCase $response
     * @param Container                                     $container
     * @param \Enlight_View_Default                         $view
     */
    public function __construct(
        \Enlight_Controller_Request_RequestTestCase $request,
        \Enlight_Controller_Response_ResponseTestCase $response,
        Container $container,
        \Enlight_View_Default $view
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->container = $container;
        $this->view = $view;
    }
}
