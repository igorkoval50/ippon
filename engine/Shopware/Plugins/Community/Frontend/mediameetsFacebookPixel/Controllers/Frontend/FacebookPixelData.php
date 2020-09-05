<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

use Shopware\mediameetsFacebookPixel\Components\Calculators\PriceValueCalculator;
use Shopware\mediameetsFacebookPixel\Components\PluginConfig;
use Shopware\mediameetsFacebookPixel\Components\Traits\ConfigHelpers;
use Shopware\mediameetsFacebookPixel\Components\Traits\WithShop;

class Shopware_Controllers_Frontend_FacebookPixelData extends Enlight_Controller_Action
{
    use WithShop, ConfigHelpers;

    /**
     * Returning data for given article ordernumber.
     */
    public function indexAction()
    {
        $container = $this->container;
        $request = $this->Request();
        $response = $this->Response();

        $shopContext = $this->getShopContext();
        $customerGroup = $shopContext->getCurrentCustomerGroup();
        $priceCalculator = new PriceValueCalculator($customerGroup);

        $pluginConfig = (new PluginConfig())->get();

        $orderNumber = trim($request->getParam('ordernumber'));
        $categoryId = $request->getParam('categoryId', null);
        $selection = $request->getParam('selection', []);
        $additionalData = $request->getParam('additionalData', []);
        $quantity = intval($request->getParam('quantity', 1));

        /** @var sArticles $articlesModule */
        $articlesModule = $container->get('modules')->Articles();

        $article = null;

        if (is_string($selection)) {
            parse_str($selection, $selection);
        }

        if (isset($selection['__csrf_token'])) {
            unset($selection['__csrf_token']);
        }

        $selection = (isset($selection['group'])) ? $selection['group'] : [];

        $articleId = $articlesModule->sGetArticleIdByOrderNumber($orderNumber);

        if ($articleId !== false) {
            $article = $articlesModule
                ->sGetArticleById($articleId, $categoryId, $orderNumber, $selection);
        }

        $data = false;

        if (! is_null($article)) {
            $identifier = $this->isOrderNumberMode($pluginConfig['productIdentifier'])
                ? 'ordernumber'
                : 'articleDetailsID';

            $priceValue = $priceCalculator->getPrice(
                $article['price_numeric'],
                $article['tax']
            );

            $data['value'] = $priceValue * $quantity;
            $data['content_name'] = $article['articleName'];
            $data['contents'][] = ['id' => $article[$identifier], 'quantity' => $quantity];
            $data['currency'] = $this->getShop()->getCurrency()->getCurrency();

            if (in_array('customization', $additionalData)) {
                $data['customization'] = $article['additionaltext'];
            }
        }

        // Disable rendering
        /* @var Enlight_Controller_Plugins_ViewRenderer_Bootstrap $viewRenderer */
        $viewRenderer = $container->get('front')
            ->Plugins()
            ->get('ViewRenderer');

        $viewRenderer->setNoRender();

        // Send JSON response
        $body = json_encode($data, JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        $response->setBody($body);
        $response->setHeader('Content-type', 'application/json', true);
    }
}
