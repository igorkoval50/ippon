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

class OptionStruct
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
     * @var bool
     */
    public $required;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $position;

    /**
     * @var string
     */
    public $defaultValue;

    /**
     * @var string
     */
    public $placeholder;

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
     * @var float
     */
    public $surchargeTaxRate;

    /**
     * @var int
     */
    public $maxTextLength;

    /**
     * @var int
     */
    public $minValue;

    /**
     * @var int
     */
    public $maxValue;

    /**
     * @var float
     */
    public $interval;

    /**
     * @var int
     */
    public $templateId;

    /**
     * @var int
     */
    public $taxId;

    /**
     * @var OptionValueStruct[]
     */
    public $values;

    /**
     * @var bool
     */
    public $isPercentageSurcharge;

    /**
     * @var CamelCaseConverter
     */
    private $camelCaseConverter;

    /**
     * @param int|null                 $id
     * @param string|null              $name
     * @param int|null                 $ordernumber
     * @param bool|null                $required
     * @param string|null              $type
     * @param int|null                 $position
     * @param string|null              $defaultValue
     * @param string|null              $placeholder
     * @param float|null               $surcharge
     * @param float|null               $netPrice
     * @param bool|null                $isOnceSurcharge
     * @param int|null                 $surchargeTaxRate
     * @param int|null                 $maxTextLength
     * @param int|null                 $minValue
     * @param int|null                 $maxValue
     * @param int|null                 $interval
     * @param int|null                 $templateId
     * @param int|null                 $taxId
     * @param bool|null                $isPercentageSurcharge
     * @param OptionValueStruct[]|null $values
     */
    public function __construct(
        $id = null,
        $name = null,
        $ordernumber = null,
        $required = null,
        $type = null,
        $position = null,
        $defaultValue = null,
        $placeholder = null,
        $surcharge = null,
        $netPrice = null,
        $isOnceSurcharge = null,
        $surchargeTaxRate = null,
        $maxTextLength = null,
        $minValue = null,
        $maxValue = null,
        $interval = null,
        $templateId = null,
        $taxId = null,
        $isPercentageSurcharge = null,
        array $values = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->ordernumber = $ordernumber;
        $this->required = $required;
        $this->type = $type;
        $this->position = $position;
        $this->defaultValue = $defaultValue;
        $this->placeholder = $placeholder;
        $this->surcharge = $surcharge;
        $this->netPrice = $netPrice;
        $this->isOnceSurcharge = $isOnceSurcharge;
        $this->surchargeTaxRate = $surchargeTaxRate;
        $this->maxTextLength = $maxTextLength;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
        $this->interval = $interval;
        $this->templateId = $templateId;
        $this->values = $values;
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

        $this->values = $this->valuesFromArray($optionArray['values']);

        return $this;
    }

    /**
     * @return OptionValueStruct[]
     */
    private function valuesFromArray(array $values)
    {
        $valueArray = [];

        foreach ($values as $value) {
            $valueArray[] = (new OptionValueStruct())->fromArray($value);
        }

        return $valueArray;
    }
}
