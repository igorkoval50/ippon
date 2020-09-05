<?php
/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiFoundation\Service;

use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\DetachedShop;
use Shopware\Models\Shop\Shop as ShopModel;

class Shop implements ShopInterface
{
    /**
     * @var ShopModel
     */
    private static $activeShop;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var \Enlight_Controller_Front
     */
    private $front;

    /**
     * Shop constructor.
     *
     * @param ModelManager              $modelManager
     * @param ContextServiceInterface   $contextService
     * @param \Enlight_Controller_Front $front
     */
    public function __construct(
        ModelManager $modelManager,
        ContextServiceInterface $contextService,
        \Enlight_Controller_Front $front
    ) {
        $this->modelManager   = $modelManager;
        $this->contextService = $contextService;
        $this->front          = $front;
    }

    /**
     * @param int $id
     *
     * @return ShopModel
     */
    public function getShop($id)
    {
        return $this->modelManager->getRepository(ShopModel::class)->find($id);
    }

    /**
     * @return DetachedShop
     */
    public function getDefaultShop()
    {
        return $this->modelManager->getRepository(ShopModel::class)->getDefault();
    }

    /**
     * This function delivers the active shop by id
     *
     * @param $id
     *
     * @return DetachedShop
     */
    public function getActiveShopById($id)
    {
        return $this->modelManager->getRepository(ShopModel::class)->getActiveById($id);
    }

    /**
     * @return DetachedShop
     */
    public function getActiveDefaultShop()
    {
        return $this->modelManager->getRepository(ShopModel::class)->getActiveDefault();
    }

    /**
     * This function delivers the *current* shop. It can also be used in the backend and api but delivers null.
     *
     * @param bool $noCache - live reload the shop instance
     *
     * @return ShopModel|null
     */
    public function getActiveShop($noCache = false)
    {
        $request    = $this->front->Request();
        $moduleName = $request instanceof \Enlight_Controller_Request_Request ? $request->getModuleName() : '';

        if (
            PHP_SAPI !== 'cli'
            && \is_string($moduleName)
            && '' !== $moduleName
            && 'backend' !== $moduleName
            && 'api' !== $moduleName
            && ($noCache
                || !self::$activeShop
                || self::$activeShop->getId() !== $this->contextService->getShopContext()->getShop()->getId())
        ) {
            try {
                self::$activeShop = $this->getShop(
                    $this->contextService->getShopContext()->getShop()->getId()
                );
            } catch (\Exception $e) {
                self::$activeShop = null;
            }
        }

        return self::$activeShop;
    }

}