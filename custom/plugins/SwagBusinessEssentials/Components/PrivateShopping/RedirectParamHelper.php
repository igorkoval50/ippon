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

namespace SwagBusinessEssentials\Components\PrivateShopping;

use RuntimeException;
use Shopware\Components\Routing\RouterInterface;

class RedirectParamHelper implements RedirectParamHelperInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function readRedirectParams(array $data)
    {
        if (!$data['redirectLogin'] && !$data['redirectRegistration']) {
            return $data;
        }

        list($data['loginControllerAction'], $data['loginParams']) = $this->convertRedirect($data['redirectLogin']);
        list($data['registerControllerAction'], $data['registerParams']) = $this->convertRedirect($data['redirectRegistration']);

        unset($data['redirectLogin'], $data['redirectRegistration']);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function convertRedirect($redirectString)
    {
        if (!$redirectString) {
            return ['', []];
        }

        $parts = explode('/', $redirectString);

        $controllerActionString = $parts[0] . '/' . $parts[1];

        // Only controller and action found
        if (count($parts) < 3) {
            return [$controllerActionString];
        }

        unset($parts[0], $parts[1]);

        $parts = array_values($parts);
        $params = $this->getParamsFromArray($parts);

        return [$controllerActionString, $params];
    }

    /**
     * {@inheritdoc}
     */
    public function getParamsFromArray(array $paramsArray)
    {
        $paramsCount = count($paramsArray);

        if ($paramsCount % 2 !== 0) {
            throw new RuntimeException('Invalid amount of array elements.');
        }

        $params = [];
        for ($i = 0; $i <= $paramsCount; $i += 2) {
            if (!isset($paramsArray[$i])) {
                break;
            }

            $params[] = [
                'key' => $paramsArray[$i],
                'value' => $paramsArray[$i + 1],
            ];
        }

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    public function buildQueryString($controllerAction, $params)
    {
        if (!$controllerAction) {
            if (!$params) {
                return '';
            }

            $controllerAction = 'account/index';
        }

        $paramString = '';

        foreach ($params as $paramSet) {
            $paramString .= $paramSet['key'] . '/' . $paramSet['value'] . '/';
        }

        return trim($controllerAction . '/' . $paramString, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrlFromRedirectConfig($redirectConfig)
    {
        if (!$redirectConfig) {
            return $this->router->assemble([
                'controller' => 'account',
                'action' => 'index',
            ]);
        }

        $parts = explode('/', $redirectConfig);
        $partsCount = count($parts);

        if ($partsCount % 2 !== 0) {
            throw new RuntimeException('Invalid amount of parameters configured');
        }

        $controller = $parts[0];
        $action = $parts[1];

        $assembleArray = [
            'controller' => $controller,
            'action' => $action,
        ];

        unset($parts[0], $parts[1]);

        // Reset array keys
        $parts = array_values($parts);
        foreach ($this->getParamsFromArray($parts) as $paramSet) {
            $assembleArray[$paramSet['key']] = $paramSet['value'];
        }

        return $this->router->assemble($assembleArray);
    }
}
