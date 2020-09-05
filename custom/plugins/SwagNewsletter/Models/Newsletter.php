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
use Shopware\Models\Newsletter\Newsletter as ModelEntity;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_campaigns_mailings")
 */
class Newsletter extends ModelEntity
{
    /**
     * INVERSE SIDE
     * Contains all the assigned \SwagNewsletter\Models\Element models.
     * The element model contains the configuration about the size and position of the element
     * and the assigned \SwagNewsletter\Models\Component which contains the data configuration.
     *
     * @ORM\OneToMany(
     *      targetEntity="SwagNewsletter\Models\Element",
     *      mappedBy="newsletter",
     *      orphanRemoval=true,
     *      cascade={"persist", "remove"}
     * )
     *
     * @var ArrayCollection
     */
    protected $elements;

    /**
     * @param ArrayCollection $elements
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
    }

    /**
     * @return ArrayCollection
     */
    public function getElements()
    {
        return $this->elements;
    }
}
