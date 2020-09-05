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

namespace SwagCustomProducts\Models;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Table(name="s_plugin_custom_products_price", indexes={@ORM\Index(name="search_idx", columns={"tax_id", "option_id", "value_id"})})
 * @ORM\Entity()
 */
class Price extends ModelEntity implements JsonSerializable
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var float
     * @ORM\Column(name="surcharge", type="float", scale=5, nullable=true)
     */
    protected $surcharge;

    /**
     * @var float
     * @ORM\Column(name="percentage", type="float", scale=2, nullable=true)
     */
    protected $percentage;

    /**
     * @var bool
     * @ORM\Column(name="is_percentage_surcharge", type="boolean", nullable=true)
     */
    protected $isPercentageSurcharge;

    /**
     * @var int
     * @ORM\Column(name="tax_id", type="integer")
     */
    protected $taxId;

    /**
     * @var string
     * @ORM\Column(name="customer_group_name", type="string", nullable=false)
     */
    protected $customerGroupName;

    /**
     * @var int
     * @ORM\Column(name="customer_group_id", type="integer")
     */
    protected $customerGroupId;

    /**
     * @var int|null
     * @ORM\Column(name="option_id", type="integer", nullable=true)
     */
    protected $optionId;

    /**
     * @var \SwagCustomProducts\Models\Option
     * @ORM\ManyToOne(targetEntity="SwagCustomProducts\Models\Option")
     * @ORM\JoinColumn(name="option_id", referencedColumnName="id")
     */
    protected $option;

    /**
     * @var int|null
     * @ORM\Column(name="value_id", type="integer", nullable=true)
     */
    protected $valueId;

    /**
     * @var \SwagCustomProducts\Models\Value
     * @ORM\ManyToOne(targetEntity="SwagCustomProducts\Models\Value")
     * @ORM\JoinColumn(name="value_id", referencedColumnName="id")
     */
    protected $value;

    /**
     * Price Clone
     */
    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return float
     */
    public function getSurcharge()
    {
        return $this->surcharge;
    }

    /**
     * @param float $surcharge
     *
     * @return $this
     */
    public function setSurcharge($surcharge)
    {
        $this->surcharge = $surcharge;

        return $this;
    }

    /**
     * @return int
     */
    public function getTaxId()
    {
        return $this->taxId;
    }

    /**
     * @param int $taxId
     *
     * @return $this
     */
    public function setTaxId($taxId)
    {
        $this->taxId = $taxId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerGroupName()
    {
        return $this->customerGroupName;
    }

    /**
     * @param string $customerGroupName
     *
     * @return $this
     */
    public function setCustomerGroupName($customerGroupName)
    {
        $this->customerGroupName = $customerGroupName;

        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->customerGroupId;
    }

    /**
     * @param int $customerGroupId
     *
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->customerGroupId = $customerGroupId;

        return $this;
    }

    /**
     * @return int
     */
    public function getOptionId()
    {
        return $this->optionId;
    }

    /**
     * @param int $optionId
     *
     * @return $this
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;

        return $this;
    }

    /**
     * @return Option
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param Option|array $option
     *
     * @return $this
     */
    public function setOption($option)
    {
        return $this->setManyToOne($option, Option::class, 'option');
    }

    /**
     * @return int|null
     */
    public function getValueId()
    {
        return $this->valueId;
    }

    /**
     * @param int|null $valueId
     *
     * @return $this
     */
    public function setValueId($valueId)
    {
        $this->valueId = $valueId;

        return $this;
    }

    /**
     * @return Value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param Value|array $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        return $this->setManyToOne($value, Value::class, 'value');
    }

    /**
     * @return bool
     */
    public function getIsPercentageSurcharge()
    {
        return $this->isPercentageSurcharge;
    }

    /**
     * @param bool $isPercentageSurcharge
     */
    public function setIsPercentageSurcharge($isPercentageSurcharge)
    {
        $this->isPercentageSurcharge = $isPercentageSurcharge;
    }

    /**
     * @return float
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param float $percentage
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
