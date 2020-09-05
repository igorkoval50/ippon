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

namespace SwagProductAdvisor\Tests\Functional\Controllers\Backend;

use PHPUnit\Framework\TestCase;
use Shopware\Tests\Functional\Traits\DatabaseTransactionBehaviour;

require_once __DIR__ . '/../../../../Controllers/Backend/Advisor.php';

class AdvisorTest extends TestCase
{
    use DatabaseTransactionBehaviour;

    public function test_getPropertiesAjaxAction_getStreamProperties()
    {
        $testDataSql = file_get_contents(__DIR__ . '/_fixtures/create_data_for_properties_test.sql');
        Shopware()->Container()->get('dbal_connection')->exec($testDataSql);

        $controller = $this->getBackendControllerAdvisor();

        $controller->Request()->setParam('streamId', 42);
        $controller->Request()->setParam('showAllProperties', 'false');

        $controller->getPropertiesAjaxAction();

        $result = $controller->View()->getAssign('data');

        static::assertCount(5, $result);
        foreach ($result as $property) {
            if ($property['name'] === 'PhpUnit_option') {
                static::fail('Property "PhpUnit_option" should not be there but it is');
            }
        }
    }

    public function test_getPropertiesAjaxAction_getAllProperties()
    {
        $testDataSql = file_get_contents(__DIR__ . '/_fixtures/create_data_for_properties_test.sql');
        Shopware()->Container()->get('dbal_connection')->exec($testDataSql);

        $controller = $this->getBackendControllerAdvisor();

        $controller->Request()->setParam('streamId', 42);
        $controller->Request()->setParam('showAllProperties', 'true');

        $controller->getPropertiesAjaxAction();

        $result = $controller->View()->getAssign('data');

        $success = false;
        foreach ($result as $property) {
            if ($property['name'] === 'PhpUnit_option') {
                $success = true;
            }
        }

        static::assertCount(6, $result);
        static::assertTrue($success);
    }

    public function test_getPropertyValuesAjaxAction_getStreamPropertyValues()
    {
        $testDataSql = file_get_contents(__DIR__ . '/_fixtures/create_data_for_properties_test.sql');
        Shopware()->Container()->get('dbal_connection')->exec($testDataSql);

        $controller = $this->getBackendControllerAdvisor();

        $controller->Request()->setParam('streamId', 42);
        $controller->Request()->setParam('propertyId', 2);
        $controller->Request()->setParam('showAllProperties', 'false');
        $controller->Request()->setParam('limit', 100);
        $controller->Request()->setParam('start', 0);

        $controller->getPropertyValuesAjaxAction();

        $result = $controller->View()->getAssign('data');

        static::assertCount(6, $result);
    }

    public function test_getPropertyValuesAjaxAction_getStreamPropertyValues_showAllPropertiesIsFalse_shouldBeEmpty()
    {
        $testDataSql = file_get_contents(__DIR__ . '/_fixtures/create_data_for_properties_test.sql');
        Shopware()->Container()->get('dbal_connection')->exec($testDataSql);

        $controller = $this->getBackendControllerAdvisor();

        $controller->Request()->setParam('streamId', 42);
        $controller->Request()->setParam('propertyId', 8);
        $controller->Request()->setParam('limit', 100);
        $controller->Request()->setParam('start', 0);
        $controller->Request()->setParam('showAllProperties', 'false');

        $controller->getPropertyValuesAjaxAction();

        $result = $controller->View()->getAssign('data');

        static::assertEmpty($result);
    }

    public function test_getPropertyValuesAjaxAction_getStreamPropertyValues_showAllPropertiesIstrue()
    {
        $testDataSql = file_get_contents(__DIR__ . '/_fixtures/create_data_for_properties_test.sql');
        Shopware()->Container()->get('dbal_connection')->exec($testDataSql);

        $controller = $this->getBackendControllerAdvisor();

        $controller->Request()->setParam('streamId', 42);
        $controller->Request()->setParam('propertyId', 8);
        $controller->Request()->setParam('limit', 100);
        $controller->Request()->setParam('start', 0);
        $controller->Request()->setParam('showAllProperties', 'true');

        $controller->getPropertyValuesAjaxAction();

        $result = $controller->View()->getAssign('data');

        static::assertCount(4, $result);
    }

    private function getBackendControllerAdvisor()
    {
        $request = new \Enlight_Controller_Request_RequestHttp();
        $response = new \Enlight_Controller_Response_ResponseHttp();

        Shopware()->Container()->get('front')->setRequest($request);
        Shopware()->Container()->get('front')->setResponse($response);

        $controller = new \Shopware_Controllers_Backend_Advisor();
        $controller->setRequest($request);
        $controller->setResponse($response);
        $controller->setView(new \Enlight_View_Default(Shopware()->Template()));
        $controller->setContainer(Shopware()->Container());

        return $controller;
    }
}
