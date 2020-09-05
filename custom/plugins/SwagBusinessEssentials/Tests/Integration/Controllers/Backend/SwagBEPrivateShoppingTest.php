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

namespace SwagBusinessEssentials\Tests\Integration\Controllers\Backend;

use Enlight_Controller_Request_RequestTestCase as TestRequest;
use Enlight_Controller_Response_ResponseTestCase as TestResponse;
use PHPUnit\Framework\TestCase;
use SwagBusinessEssentials\Tests\WebTestCaseTrait;

class SwagBEPrivateShoppingTest extends TestCase
{
    use WebTestCaseTrait;

    public function test_getControllers()
    {
        $request = new TestRequest();
        $request->setRequestUri('backend/SwagBEPrivateShopping/getControllers');

        $response = new TestResponse();

        $this->dispatch($request, $response);

        static::assertEquals(200, $response->getHttpResponseCode());
    }

    public function test_getDetail()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/PrivateShoppingFixtures.sql'));

        $request = new TestRequest();
        $request->setRequestUri('backend/SwagBEPrivateShopping/detail');
        $request->setParam('id', 1000);

        $response = new TestResponse();

        $this->dispatch($request, $response);

        static::assertEquals(200, $response->getHttpResponseCode());
    }
}
