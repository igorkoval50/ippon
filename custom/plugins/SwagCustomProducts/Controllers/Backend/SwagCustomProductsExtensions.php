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

use Doctrine\DBAL\Connection;
use Shopware\Bundle\MediaBundle\MediaService;
use Shopware\Components\CSRFWhitelistAware;
use SwagCustomProducts\Components\Services\ZipServiceInterface;

class Shopware_Controllers_Backend_SwagCustomProductsExtensions extends Shopware_Controllers_Backend_ExtJs implements CSRFWhitelistAware
{
    /**
     * {@inheritdoc}
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'downloadSvg',
        ];
    }

    /**
     * Provides an action which can be triggered using an AJAX request to get the basic
     * custom product information.
     *
     * We're using it in the product module to check if a product has an associated custom product template.
     */
    public function getTemplateByProductIdAction()
    {
        $productId = (int) $this->Request()->getParam('productId');

        /** @var Connection $connection */
        $connection = $this->container->get('dbal_connection');
        $query = $connection->createQueryBuilder();

        $data = $query->select('template.*')
            ->from('s_plugin_custom_products_template', 'template')
            ->join('template', 's_plugin_custom_products_template_product_relation', 'product', 'product.template_id = template.id')
            ->where('product.article_id = :productId')
            ->setParameter('productId', $productId)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);

        $this->view->assign(['success' => true, 'data' => $data]);
    }

    /**
     * Get the productConfiguration by hash
     */
    public function getConfigurationByHashAction()
    {
        $hash = (string) $this->Request()->getParam('hash');
        $customProductsService = $this->container->get('custom_products.custom_products_option_repository');

        $this->View()->assign('data', $customProductsService->getOptionsFromHash($hash));
    }

    /**
     * Creates a zip file from the svg and send it as download
     */
    public function downloadSvgAction()
    {
        $path = $this->request->getParam('path');
        $value = $this->request->getParam('value');

        if ($value) {
            /** @var MediaService $mediaService */
            $mediaService = $this->container->get('shopware_media.media_service');
            $path = $mediaService->encode($value);
        }

        if (!$path || !is_file($path)) {
            return;
        }

        /** @var ZipServiceInterface $zipService */
        $zipService = $this->container->get('custom_products.zip_service');
        $zipName = md5($path);
        $filename = $zipService->createZipFile($path, $zipName);

        $this->Response()
            ->setHeader('Content-type', 'application/zip')
            ->setHeader('Content-Transfer-Encoding', 'binary')
            ->setHeader('Content-disposition', 'attachment; filename="' . basename($filename) . '"')
            ->setHeader('Content-Length', filesize($filename))
            ->sendHeaders();

        readfile($filename);

        $zipService->deleteZipFile($filename);

        // add exit() function because it is required to open the zip file on windows
        exit();
    }
}
