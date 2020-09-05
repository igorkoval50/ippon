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

namespace SwagCustomProducts\Structs;

use SwagCustomProducts\Components\CamelCaseConverter;

class OptionValueStruct
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $ordernumber;

    /**
     * @var string
     */
    public $value;

    /**
     * @var bool
     */
    public $isDefaultValue;

    /**
     * @var int
     */
    public $position;

    /**
     * @var float
     */
    public $surcharge;

    /**
     * @var float
     */
    public $netPrice;

    /**
     * @var bool
     */
    public $isOnceSurcharge;

    /**
     * @var int
     */
    public $surchargeTaxRate;

    /**
     * @var int
     */
    public $taxId;

    /**
     * @var bool
     */
    public $isPercentageSurcharge;

    /**
     * @var CamelCaseConverter
     */
    private $camelCaseConverter;

    /**
     * @param int|null    $id
     * @param string|null $name
     * @param string|null $ordernumber
     * @param string|null $value
     * @param bool|null   $isDefaultValue
     * @param int|null    $position
     * @param float|null  $surcharge
     * @param null        $netPrice
     * @param bool|null   $isOnceSurcharge
     * @param int|null    $surchargeTaxRate
     * @param bool|null   $isPercentageSurcharge
     * @param null        $taxId
     */
    public function __construct(
        $id = null,
        $name = null,
        $ordernumber = null,
        $value = null,
        $isDefaultValue = null,
        $position = null,
        $surcharge = null,
        $netPrice = null,
        $isOnceSurcharge = null,
        $surchargeTaxRate = null,
        $isPercentageSurcharge = null,
        $taxId = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->ordernumber = $ordernumber;
        $this->value = $value;
        $this->isDefaultValue = $isDefaultValue;
        $this->position = $position;
        $this->surcharge = $surcharge;
        $this->netPrice = $netPrice;
        $this->isOnceSurcharge = $isOnceSurcharge;
        $this->surchargeTaxRate = $surchargeTaxRate;
        $this->taxId = $taxId;
        $this->isPercentageSurcharge = $isPercentageSurcharge;
        $this->camelCaseConverter = new CamelCaseConverter();
    }

    /**
     * @return $this
     */
    public function fromArray(array $optionArray)
    {
        foreach ($optionArray as $key => $value) {
            $newKey = $this->camelCaseConverter->convert($key);
            $this->$newKey = $value;
        }

        return $this;
    }
}
