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

namespace SwagBusinessEssentials\Models\Template;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_core_plugins_b2b_tpl_variables", options={"collate":"utf8_unicode_ci"})
 */
class TplVariables extends ModelEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="variable", type="string", nullable=false)
     */
    protected $variable;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=false)
     */
    protected $description;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Customer\Group")
     * @ORM\JoinTable(name="s_core_plugins_b2b_tpl_config",
     *      joinColumns={@ORM\JoinColumn(name="variable_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="customergroup_id", referencedColumnName="id")}
     * )
     */
    protected $customerGroups;

    /**
     * TplVariables constructor.
     */
    public function __construct()
    {
        $this->customerGroups = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $variable
     *
     * @return $this
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;

        return $this;
    }

    /**
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection
     *
     * @return $this
     */
    public function setCustomerGroups($customerGroups)
    {
        $this->customerGroups = $customerGroups;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCustomerGroups()
    {
        return $this->customerGroups;
    }
}
