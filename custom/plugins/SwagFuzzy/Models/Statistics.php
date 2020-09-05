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

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Shopware SwagFuzzy Plugin - Model Statistics
 *
 * @category Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_swag_fuzzy_statistics")
 */
class Statistics extends ModelEntity
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
     * @var int
     *
     * @ORM\Column(type="integer");
     */
    private $shopId;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Shop")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    private $shop;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $searchTerm;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $firstSearchDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $lastSearchDate;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $searchesCount;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $resultsCount;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @param mixed $shop
     *
     * @return $this
     */
    public function setShop($shop)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @param string $searchTerm
     *
     * @return $this
     */
    public function setSearchTerm($searchTerm)
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFirstSearchDate()
    {
        return $this->firstSearchDate;
    }

    /**
     * @param DateTime $firstSearchDate
     *
     * @return $this
     */
    public function setFirstSearchDate($firstSearchDate)
    {
        $this->firstSearchDate = $firstSearchDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastSearchDate()
    {
        return $this->lastSearchDate;
    }

    /**
     * @param DateTime $lastSearchDate
     *
     * @return $this
     */
    public function setLastSearchDate($lastSearchDate)
    {
        $this->lastSearchDate = $lastSearchDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getSearchesCount()
    {
        return $this->searchesCount;
    }

    /**
     * @param int $searchesCount
     *
     * @return $this
     */
    public function setSearchesCount($searchesCount)
    {
        $this->searchesCount = $searchesCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getResultsCount()
    {
        return $this->resultsCount;
    }

    /**
     * @param int $resultsCount
     *
     * @return $this
     */
    public function setResultsCount($resultsCount)
    {
        $this->resultsCount = $resultsCount;

        return $this;
    }
}
