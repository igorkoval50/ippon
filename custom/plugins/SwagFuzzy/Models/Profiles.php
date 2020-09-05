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

namespace SwagFuzzy\Models;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Shopware SwagFuzzy Plugin - Profiles Model
 *
 * @category Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_swag_fuzzy_profiles")
 */
class Profiles extends ModelEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, )
     */
    private $standard = 0;

    /**
     * @var string settings
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $settings;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $relevance;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $searchTables;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function getStandard()
    {
        return $this->standard;
    }

    /**
     * @param bool $standard
     *
     * @return $this
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;

        return $this;
    }

    /**
     * @return string
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param string $settings
     *
     * @return $this
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * @return string
     */
    public function getRelevance()
    {
        return $this->relevance;
    }

    /**
     * @param string $relevance
     *
     * @return $this
     */
    public function setRelevance($relevance)
    {
        $this->relevance = $relevance;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchTables()
    {
        return $this->searchTables;
    }

    /**
     * @param string $searchTables
     *
     * @return $this
     */
    public function setSearchTables($searchTables)
    {
        $this->searchTables = $searchTables;

        return $this;
    }
}
