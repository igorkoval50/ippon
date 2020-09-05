<?php
/**
 * Copyright notice
 *
 * (c) 2009-2016 Net Inventors - Agentur für digitale Medien GmbH
 * All rights reserved
 *
 * This script is part of the Spirit-Project.
 * The Spirit-Project is property of the Net Inventors GmbH and
 * may not be used in projects not related to the Net Inventors
 * without explicit permission by the authors.
 *
 * PHP version 5
 *
 * @package    NetiFoundation
 * @subpackage NetiFoundation/Helper.php
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */

namespace NetiFoundation\Service;

/**
 * Class Helper
 *
 * @deprecated The methods of the helper class have been moved to more appropriate classes. Use those instead.
 * @package    NetiFoundation\Service
 */
class Helper implements HelperInterface
{
    /**
     * @var ShopInterface
     */
    protected $shopService;

    /**
     * @var ApplicationInterface
     */
    private $applicationService;

    /**
     * @var StringOperationInterface
     */
    private $stringOperationService;

    /**
     * @var BasketInterface
     */
    private $basketService;

    /**
     * @var ThemeInterface
     */
    private $themeService;

    /**
     * @var ArticleInterface
     */
    private $articleService;

    /**
     * Helper constructor.
     *
     * @param ShopInterface            $shopService
     * @param ApplicationInterface     $applicationService
     * @param StringOperationInterface $stringOperationService
     * @param BasketInterface          $basketService
     * @param ThemeInterface           $themeService
     * @param ArticleInterface         $articleService
     */
    public function __construct(
        ShopInterface $shopService,
        ApplicationInterface $applicationService,
        StringOperationInterface $stringOperationService,
        BasketInterface $basketService,
        ThemeInterface $themeService,
        ArticleInterface $articleService
    ) {
        $this->shopService            = $shopService;
        $this->applicationService     = $applicationService;
        $this->stringOperationService = $stringOperationService;
        $this->basketService          = $basketService;
        $this->themeService           = $themeService;
        $this->articleService         = $articleService;
    }

    /**
     * @inheritdoc
     */
    public function getShop($id)
    {
        return $this->shopService->getShop($id);
    }

    /**
     * @inheritdoc
     */
    public function getDefaultShop()
    {
        return $this->shopService->getDefaultShop();
    }

    /**
     * @inheritdoc
     */
    public function getActiveShopById($id)
    {
        return $this->shopService->getActiveShopById($id);
    }

    /**
     * @inheritdoc
     */
    public function getActiveDefaultShop()
    {
        return $this->shopService->getActiveDefaultShop();
    }

    /**
     * @inheritdoc
     */
    public function getActiveShop()
    {
        return $this->shopService->getActiveShop();
    }

    /**
     * @inheritdoc
     */
    public function assertMinimumVersion($requiredVersion, $includeRequiredVersion = true)
    {
        return $this->applicationService->assertMinimumVersion($requiredVersion, $includeRequiredVersion);
    }

    /**
     * @inheritdoc
     */
    public function assertMaximumVersion($requiredVersion, $includeRequiredVersion = true)
    {
        return $this->applicationService->assertMaximumVersion($requiredVersion, $includeRequiredVersion);
    }

    /**
     * @inheritdoc
     */
    public function getBasketValue(
        $excludedModus = [],
        $includedModus = [],
        $filters = [],
        $additionalSql = '',
        $sessionID = null,
        $splitByTaxRates = false
    ) {
        return $this->basketService->getBasketValue(
            $excludedModus,
            $includedModus,
            $filters,
            $additionalSql,
            $sessionID,
            $splitByTaxRates
        );
    }

    /**
     * @inheritdoc
     */
    public function getThemeConfiguration($shopId = null)
    {
        return $this->themeService->getThemeConfiguration($shopId);
    }

    /**
     * @inheritdoc
     */
    public function getNewArticleData($prefix = null)
    {
        return $this->articleService->getNewArticleData();
    }

    /**
     * @inheritdoc
     */
    public function decamelize($input, $char = '_')
    {
        return $this->stringOperationService->decamelize($input, $char);
    }
}
