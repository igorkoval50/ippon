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
 * Shopware SwagFuzzy Plugin - SearchTables Model
 *
 * @category Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_search_tables")
 */
class SearchTables extends ModelEntity
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
     * @ORM\Column(name="`table`")
     */
    private $table;

    /**
     * @var string
     *
     * @ORM\Column(name="referenz_table")
     */
    private $referenceTable;

    /**
     * @var string
     *
     * @ORM\Column(name="foreign_key")
     */
    private $foreignKey;

    /**
     * @var string
     *
     * @ORM\Column(name="`where`")
     */
    private $additionalCondition;

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
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceTable()
    {
        return $this->referenceTable;
    }

    /**
     * @param string $referenceTable
     *
     * @return $this
     */
    public function setReferenceTable($referenceTable)
    {
        $this->referenceTable = $referenceTable;

        return $this;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @param string $foreignKey
     *
     * @return $this
     */
    public function setForeignKey($foreignKey)
    {
        $this->foreignKey = $foreignKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalCondition()
    {
        return $this->additionalCondition;
    }

    /**
     * @param string $additionalCondition
     *
     * @return $this
     */
    public function setAdditionalCondition($additionalCondition)
    {
        $this->additionalCondition = $additionalCondition;

        return $this;
    }
}
