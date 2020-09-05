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

namespace SwagNewsletter\Components\LiveShopping;

use sArticles;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Plugin\Plugin;
use SwagLiveShopping\Components\LiveShoppingInterface;
use SwagLiveShopping\Models\LiveShopping;
use SwagLiveShopping\Models\Repository;
use SwagNewsletter\Components\DependencyProviderInterface;

class LiveShoppingRepository implements LiveShoppingRepositoryInterface
{
    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var LiveShoppingInterface|null
     */
    private $liveShoppingComponent;

    /**
     * @var sArticles
     */
    private $sArticles;

    /**
     * @var DependencyProviderInterface
     */
    private $dependencyProvider;

    /**
     * @param ModelManager                $modelManager
     * @param DependencyProviderInterface $dependencyProvider
     * @param LiveShoppingInterface|null  $liveShoppingComponent
     */
    public function __construct(
        ModelManager $modelManager,
        DependencyProviderInterface $dependencyProvider,
        LiveShoppingInterface $liveShoppingComponent = null
    ) {
        $this->modelManager = $modelManager;
        $this->liveShoppingComponent = $liveShoppingComponent;
        $this->dependencyProvider = $dependencyProvider;
    }

    /**
     * @return Plugin|null
     */
    public function getLiveShoppingPlugin()
    {
        $this->checkLiveShoppingInstalled();

        return $this->modelManager->getRepository(Plugin::class)->findOneBy([
            'label' => self::LIVE_SHOPPING_LABEL,
        ]);
    }

    /**
     * @param array $filter
     *
     * @return array
     */
    public function getProducts(array $filter)
    {
        $this->checkLiveShoppingInstalled();

        /** @var Repository $liveShoppingRepository */
        $liveShoppingRepository = $this->modelManager->getRepository(LiveShopping::class);
        $builder = $liveShoppingRepository->getLiveShoppingProductBuilder($filter);

        return $builder->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
    public function getLiveProducts()
    {
        $this->checkLiveShoppingInstalled();

        $categoryId = $this->dependencyProvider->getShop()->getCategory()->getId();

        /** @var Repository $liveShoppingRepository */
        $liveShoppingRepository = $this->modelManager->getRepository(LiveShopping::class);

        $builder = $liveShoppingRepository->getActiveLiveShoppingByMainCategory($categoryId);
        $query = $builder->getQuery();

        $result = $query->getArrayResult();
        $liveProducts = [];
        foreach ($result as $row) {
            $liveShoppingCheck = $this->liveShoppingComponent->getActiveLiveShoppingById($row['prices'][0]['liveShoppingId']);
            $liveProduct = $this->getArticleCore()->sGetArticleById($row['articleId']);
            if ($liveShoppingCheck) {
                $liveProductData = $this->liveShoppingComponent->getLiveShoppingArrayData($liveShoppingCheck);
                $liveProduct['liveShopping'] = $liveProductData;

                $liveProduct = $this->getLimitedVariantsData($liveShoppingCheck, $liveProduct);

                $liveProducts[] = $liveProduct;
            }
        }

        return $liveProducts;
    }

    /**
     * @return sArticles
     */
    protected function getArticleCore()
    {
        $this->sArticles = $this->dependencyProvider->getModule('sArticles');

        return $this->sArticles;
    }

    /**
     * Changes product's information, based on active ls variant
     *
     * @param LiveShopping $liveShopping
     * @param array        $data
     *
     * @return array
     */
    private function getLimitedVariantsData(LiveShopping $liveShopping, array $data)
    {
        $variant = $this->liveShoppingComponent->getProductByLiveShopping($liveShopping);
        $liveShopping->setReferenceUnitPrice(
            $this->liveShoppingComponent->getReferenceUnitPriceForLiveShopping($liveShopping, $variant)
        );

        $productData['additionaltext'] = $variant->getAdditionalText();
        $productData['referenceunit'] = (float) $variant->getReferenceUnit();
        $productData['purchaseunit'] = (float) $variant->getPurchaseUnit();
        $productData['liveShopping'] = $this->liveShoppingComponent->getLiveShoppingArrayData($liveShopping);

        $data = array_merge($data, $productData);

        return $data;
    }

    /**
     * @throws LiveShoppingCompatibilityException
     *
     * @return bool
     */
    private function checkLiveShoppingInstalled()
    {
        if ($this->liveShoppingComponent === null) {
            throw new LiveShoppingCompatibilityException('LiveShopping is not installed or inactive.');
        }

        return true;
    }
}
