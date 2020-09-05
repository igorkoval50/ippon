<?php declare(strict_types=1);
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

namespace SwagPromotion\Components\Promotion\DiscountCommand\Command;

class FreeGoodsBundleCommand implements Command
{
    const FREE_GOODS_BUNDLE_COMMAND_NAME = 'freeGoodsBundleCommand';

    /**
     * @var float
     */
    private $discountAmount;

    /**
     * @var int
     */
    private $freeGoodsAmount;

    public function __construct(float $discountAmount, int $freeGoodsAmount)
    {
        $this->discountAmount = $discountAmount;
        $this->freeGoodsAmount = $freeGoodsAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::FREE_GOODS_BUNDLE_COMMAND_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $name === $this->getName();
    }

    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    public function getFreeGoodsAmount(): int
    {
        return $this->freeGoodsAmount;
    }
}
