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

class SwagBEPrivateRegisterTest extends TestCase
{
    use WebTestCaseTrait;

    public function test_detailAction()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/PrivateRegisterFixtures.sql'));

        $request = new TestRequest();
        $request->setRequestUri('backend/SwagBEPrivateRegister/detail');
        $request->setParam('id', 1);
        $request->setParam('customerGroup', 'H');

        $response = new TestResponse();

        $result = $this->dispatch($request, $response);

        static::assertEquals(200, $response->getHttpResponseCode());
        static::assertTrue($result->getAssign('success'));

        static::assertArraySubset([
            'id' => 1001,
            'customerGroup' => 'H',
            'allowRegister' => false,
            'requireUnlock' => true,
            'assignGroupBeforeUnlock' => 'EK',
            'registerTemplate' => '',
            'emailTemplateDeny' => 'sREGISTERCONFIRMATION',
            'emailTemplateAllow' => 'sORDER',
            'displayLink' => 'register/index/sValidation/H',
        ], $result->getAssign('data'));
    }
}
