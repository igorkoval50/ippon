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

namespace SwagNewsletter\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;
use Shopware\Models\Newsletter\Newsletter as NewsletterModel;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_swag_newsletter_element")
 */
class Element extends ModelEntity
{
    /**
     * OWNING SIDE
     * Contains the assigned \Models\Newsletter\Newsletter
     * which can be configured in the backend newsletter module.
     * The assigned newsletter contains the definition of the newsletter elements.
     * The element model is the owning side (primary key in this table) of the association between
     * newsletter and grid elements.
     *
     * @var NewsletterModel
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Newsletter\Newsletter", inversedBy="elements", cascade={"persist"})
     * @ORM\JoinColumn(name="newsletterID", referencedColumnName="id")
     */
    protected $newsletter;

    /**
     * Contains the assigned \SwagNewsletter\Models\Component
     * which can be configured in the backend newsletter module.
     * The assigned library component contains the data definition for the grid element.
     *
     * @var Component
     * @ORM\OneToOne(targetEntity="\SwagNewsletter\Models\Component")
     * @ORM\JoinColumn(name="componentID", referencedColumnName="id")
     */
    protected $component;

    /**
     * INVERSE SIDE
     *
     * @ORM\OneToMany(
     *     targetEntity="SwagNewsletter\Models\Data",
     *     mappedBy="element",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     * )
     *
     * @var ArrayCollection
     */
    protected $data;
    /**
     * Unique identifier field of the element model.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Id of the associated \Models\Newsletter\Newsletter model.
     * The newsletter contains all defined grid elements which defined
     * over the newsletter backend module.
     *
     * @var int
     *
     * @ORM\Column(name="newsletterID", type="integer", nullable=false)
     */
    private $newsletterId;

    /**
     * Id of the associated \SwagNewsletter\Models\Component model.
     * The library component contains the data configuration for the grid element (product, banner, ...).
     *
     * @var int
     *
     * @ORM\Column(name="componentID", type="integer", nullable=false)
     */
    private $componentId;

    /**
     * Defines on which row the element starts.
     *
     * @var int
     *
     * @ORM\Column(name="start_row", type="integer", nullable=false)
     */
    private $startRow;

    /**
     * Defines on which col the element starts.
     *
     * @var int
     *
     * @ORM\Column(name="start_col", type="integer", nullable=false)
     */
    private $startCol;

    /**
     * Defines on which row the element ends.
     *
     * @var int
     *
     * @ORM\Column(name="end_row", type="integer", nullable=false)
     */
    private $endRow;

    /**
     * Defines on which col the element ends.
     *
     * @var int
     *
     * @ORM\Column(name="end_col", type="integer", nullable=false)
     */
    private $endCol;

    /**
     * Unique identifier field of the element model.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Defines on which column the element starts.
     *
     * @return int
     */
    public function getStartRow()
    {
        return $this->startRow;
    }

    /**
     * Defines on which column the element starts.
     *
     * @param int $startRow
     */
    public function setStartRow($startRow)
    {
        $this->startRow = $startRow;
    }

    /**
     * Defines on which row the element starts.
     *
     * @return int
     */
    public function getStartCol()
    {
        return $this->startCol;
    }

    /**
     * Defines on which row the element starts.
     *
     * @param int $startCol
     */
    public function setStartCol($startCol)
    {
        $this->startCol = $startCol;
    }

    /**
     * @return int
     */
    public function getEndRow()
    {
        return $this->endRow;
    }

    /**
     * @param int $endRow
     */
    public function setEndRow($endRow)
    {
        $this->endRow = $endRow;
    }

    /**
     * @return int
     */
    public function getEndCol()
    {
        return $this->endCol;
    }

    /**
     * @param int $endCol
     */
    public function setEndCol($endCol)
    {
        $this->endCol = $endCol;
    }

    /**
     * Contains the assigned \Shopware\Models\Newsletter\Newsletter
     * which can be configured in the backend newsletter module.
     * The assigned grid contains the definition of the newsletter elements.
     * The newsletter model is the owning side (primary key in this table) of the association between
     * newsletter and grid.
     *
     * @return NewsletterModel
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Contains the assigned \Shopware\Models\Newsletter\Newsletter
     * which can be configured in the backend newsletter module.
     * The assigned newsletter contains the definition of the newsletter elements.
     * The newsletter model is the owning side (primary key in this table) of the association between
     * newsletter and grid elements.
     *
     * @param NewsletterModel $newsletter
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * @return ArrayCollection
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param ArrayCollection|array|null $data
     *
     * @return ModelEntity
     */
    public function setData($data)
    {
        return $this->setOneToMany($data, Data::class, 'data', 'element');
    }

    /**
     * Contains the assigned \SwagNewsletter\Models\Component
     * which can be configured in the backend newsletter module.
     * The assigned library component contains the data definition for the grid element.
     *
     * @return Component
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * Contains the assigned \SwagNewsletter\Models\Component
     * which can be configured in the backend newsletter module.
     * The assigned library component contains the data definition for the grid element.
     *
     * @param Component $component
     */
    public function setComponent($component)
    {
        $this->component = $component;
    }
}
