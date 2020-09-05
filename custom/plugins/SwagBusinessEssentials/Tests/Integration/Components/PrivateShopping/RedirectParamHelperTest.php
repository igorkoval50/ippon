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

namespace SwagBusinessEssentials\Tests\Integration\Components\PrivateShopping;

use PHPUnit\Framework\TestCase;
use Shopware\Components\Routing\Context;
use Shopware\Components\Routing\Router;
use SwagBusinessEssentials\Components\PrivateShopping\RedirectParamHelper;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

class RedirectParamHelperTest extends TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var RedirectParamHelper
     */
    private $service;

    public function test_readRedirectParams_with_invalid_data()
    {
        $result = $this->service->readRedirectParams(
            ['redirectLogin' => '', 'redirectRegistration' => '']
        );

        static::assertEquals([
            'redirectLogin' => '',
            'redirectRegistration' => '',
        ], $result);
    }

    public function test_readRedirectParams()
    {
        $result = $this->service->readRedirectParams([
            'redirectLogin' => 'PrivateLogin/index/some/param',
            'redirectRegistration' => 'PrivateRegister/index/some/param',
        ]);

        static::assertEquals([
            'loginControllerAction' => 'PrivateLogin/index',
            'loginParams' => [
                [
                    'key' => 'some',
                    'value' => 'param',
                ],
            ],
            'registerControllerAction' => 'PrivateRegister/index',
            'registerParams' => [
                [
                    'key' => 'some',
                    'value' => 'param',
                ],
            ],
        ], $result);
    }

    public function test_convertRedirect_with_empty_string()
    {
        $result = $this->service->convertRedirect('');
        static::assertEquals(['', []], $result);
    }

    public function test_convertRedirect_less_than_3_parts()
    {
        $result = $this->service->convertRedirect('PrivateLogin/test');
        static::assertEquals([
            'PrivateLogin/test',
        ], $result);
    }

    public function test_convertRedirect_should_convert_paramerter()
    {
        $result = $this->service->convertRedirect('PrivateLogin/test/some/param');

        static::assertEquals(
            [
                'PrivateLogin/test',
                [
                    ['key' => 'some', 'value' => 'param'],
                ],
            ],
            $result
        );
    }

    public function test_buildQueryString()
    {
        $result = $this->service->buildQueryString(
            'PrivateLogin/test',
            [['key' => 'some', 'value' => 'param']]
        );

        static::assertEquals('PrivateLogin/test/some/param', $result);
    }

    public function test_buildUrlFromRedirectConfig_without_config()
    {
        $result = $this->service->buildUrlFromRedirectConfig('');

        static::assertEquals(['controller' => 'account', 'action' => 'index'], $result);
    }

    public function test_buildUrlFromRedirectConfig()
    {
        $result = $this->service->buildUrlFromRedirectConfig('PrivateLogin/index/some/param');

        static::assertEquals([
            'controller' => 'PrivateLogin',
            'action' => 'index',
            'some' => 'param',
        ], $result);
    }

    /**
     * @before
     */
    protected function createServiceBefore()
    {
        $this->service = new RedirectParamHelper(
            new RouterMock()
        );
    }
}

class RouterMock extends Router
{
    public function __construct()
    {
    }

    public function assemble($userParams = [], Context $context = null)
    {
        return $userParams;
    }
}
