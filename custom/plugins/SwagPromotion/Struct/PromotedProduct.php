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

namespace SwagPromotion\Struct;

class PromotedProduct
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $basketItemId;

    /**
     * @var int
     */
    private $basketItemAttributeId;

    /**
     * @var string
     */
    private $ordernumber;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $quantity;

    /**
     * @var float
     */
    private $price;

    /**
     * @var float
     */
    private $netprice;

    /**
     * @var float
     */
    private $discount;

    /**
     * @var float
     */
    private $directDiscount;

    /**
     * @var float
     */
    private $taxRate;

    public function __construct(array $data)
    {
        $this->productId = (int) $data['articleID'];
        $this->ordernumber = (string) $data['ordernumber'];
        $this->name = $data['basketItemName'];
        $this->basketItemId = (int) $data['basketItemId'];
        $this->basketItemAttributeId = (int) $data['basketAttribute::id'];
        $this->quantity = (float) $data['quantity'];
        $this->price = (float) $data['price'];
        $this->netprice = (float) $data['netprice'];
        $this->discount = 0;
        $this->directDiscount = 0;
        $this->taxRate = (float) $data['tax_rate'];
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getOrdernumber()
    {
        return $this->ordernumber;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getBasketItemId()
    {
        return $this->basketItemId;
    }

    /**
     * @return int
     */
    public function getBasketItemAttributeId()
    {
        return $this->basketItemAttributeId;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getNetPrice()
    {
        return $this->netprice;
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @param float $discount
     */
    public function increaseDiscount($discount)
    {
        $this->discount += $discount;
    }

    /**
     * @return float
     */
    public function getDirectDiscount()
    {
        return $this->directDiscount;
    }

    /**
     * @param float $directDiscount
     */
    public function setDirectDiscount($directDiscount)
    {
        $this->directDiscount = $directDiscount;
    }

    /**
     * @param float $directDiscount
     */
    public function increaseDirectDiscount($directDiscount)
    {
        $this->directDiscount += $directDiscount;
    }

    /**
     * @return float
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }
}
