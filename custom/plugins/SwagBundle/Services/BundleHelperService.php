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

namespace SwagBundle\Services;

use SwagBundle\Components\BundleComponentInterface;
use SwagBundle\Models\Price;

class BundleHelperService implements BundleHelperServiceInterface
{
    /**
     * @var \Zend_Currency
     */
    private $currency;

    /**
     * BundleHelperService constructor.
     */
    public function __construct(\Zend_Currency $currency)
    {
        $this->currency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function canConfigBeAdded($bundleType, array $selection, $key)
    {
        if ($bundleType === BundleComponentInterface::NORMAL_BUNDLE) {
            return true;
        }

        $bundleProductID = $this->getBundleProductId($key);

        if ($bundleProductID === 0) {
            return true;
        }

        return $this->isBundleProductInSelection($bundleProductID, $selection);
    }

    /**
     * {@inheritdoc}
     */
    public function isBundleProductInSelection($bundleProductID, array $selection)
    {
        $ids = array_map(function ($bundleProduct) {
            return $bundleProduct->getId();
        }, $selection);

        return in_array($bundleProductID, $ids, true);
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigParameter($key)
    {
        return (bool) preg_match(self::GROUP_PREFIX_REGEX, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareArrayKey($key)
    {
        return (int) preg_replace(self::GROUP_PREFIX_REGEX, '', $key);
    }

    /**
     * {@inheritdoc}
     */
    public function getBundleProductId($key)
    {
        $suffixRegEx = '/::(\d*)::(\d*)$/';

        return (int) str_replace(self::GROUP_PREFIX, '', preg_replace($suffixRegEx, '', $key, 1));
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId($key)
    {
        $suffixRegEx = '/::(\d*)$/';

        return str_replace(self::GROUP_PREFIX, '', preg_replace($suffixRegEx, '', $key, 1));
    }

    /**
     * {@inheritdoc}
     */
    public function createPrice($isTaxFree, Price $price, array $discount)
    {
        $net = $price->getNetPrice() + $discount['net'];
        $gross = $price->getGrossPrice() + $discount['gross'];

        return [
            'price' => $this->currency->toCurrency($isTaxFree ? $price->getNetPrice() : $price->getGrossPrice()),
            'regularPrice' => $this->currency->toCurrency($isTaxFree ? $net : $gross),
        ];
    }
}
