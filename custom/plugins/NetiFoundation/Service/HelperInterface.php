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
 * @subpackage NetiFoundation/HelperInterface.php
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */

namespace NetiFoundation\Service;

use NetiFoundation\Struct\NewArticleData;
use Shopware\Models\Shop\DetachedShop;
use Shopware\Models\Shop\Shop as ShopModel;

/**
 * Interface HelperInterface
 *
 * @deprecated The methods of the helper class have been moved to more appropriate classes. Use those instead.
 * @package NetiFoundation\Service
 */
interface HelperInterface
{
    /**
     * @deprecated Check "@see" for replacement.
     * @see        ShopInterface::getDefaultShop()
     *
     * @return DetachedShop
     */
    public function getDefaultShop();

    /**
     * @deprecated Check "@see" for replacement.
     * @see        ShopInterface::getShop()
     *
     * @param int $id
     *
     * @return ShopModel
     */
    public function getShop($id);

    /**
     * @deprecated Check "@see" for replacement.
     * @see        ShopInterface::getActiveDefaultShop()
     *
     * @return DetachedShop
     */
    public function getActiveDefaultShop();

    /**
     * @deprecated Check "@see" for replacement.
     * @see        ShopInterface::getActiveShopById()
     *
     * @param $id
     *
     * @return DetachedShop
     */
    public function getActiveShopById($id);

    /**
     * @deprecated Check "@see" for replacement.
     * @see        ShopInterface::getActiveShop()
     *
     * @return ShopModel
     */
    public function getActiveShop();

    /**
     * @deprecated Check "@see" for replacement.
     * @see        ApplicationInterface::assertMinimumVersion()
     *
     * @param string $requiredVersion
     * @param bool   $includeRequiredVersion
     *
     * @return bool|mixed
     */
    public function assertMinimumVersion($requiredVersion, $includeRequiredVersion = true);

    /**
     * @deprecated Check "@see" for replacement.
     * @see        ApplicationInterface::assertMaximumVersion()
     *
     * @param string $requiredVersion
     * @param bool   $includeRequiredVersion
     *
     * @return bool|mixed
     */
    public function assertMaximumVersion($requiredVersion, $includeRequiredVersion = true);

    /**
     * @deprecated check "@see" for replacement
     * @see        BasketInterface::getBasketValue()
     *
     * @param array  $excludedModus
     * @param array  $includedModus
     * @param array  $filters
     * @param string $additionalSql
     * @param string $sessionID
     * @param bool   $splitByTaxRates
     *
     * @return array|float
     */
    public function getBasketValue(
        $excludedModus = [],
        $includedModus = [],
        $filters = [],
        $additionalSql = '',
        $sessionID = null,
        $splitByTaxRates = false
    );

    /**
     * @deprecated check "@see" for replacement
     * @see        ThemeInterface::getThemeConfiguration()
     *
     * taken from \Shopware\Components\Theme\EventListener\ConfigLoader::onDispatch(), which can't be called directly
     *
     * @param int|null $shopId
     *
     * @return array
     * @throws \Exception
     */
    public function getThemeConfiguration($shopId = null);

    /**
     * @deprecated check "@see" for replacement
     * @see ArticleInterface::getNewArticleData()
     *
     * @param string|null $prefix
     *
     * @return NewArticleData
     */
    public function getNewArticleData($prefix = null);

    /**
     * @deprecated check "@see" for replacement
     * @see        StringOperationInterface::decamelize()
     *
     * @param string $input
     * @param string $char
     *
     * @return string
     */
    public function decamelize($input, $char = '_');
}
