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
 * Shopware SwagFuzzy Plugin - Synonyms Model
 *
 * @category Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_swag_fuzzy_synonyms")
 */
class Synonyms extends ModelEntity
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
     * @ORM\Column(type="integer")
     */
    private $synonymGroupId;

    /**
     * @var SynonymGroups
     *
     * @ORM\ManyToOne(
     *      targetEntity="SwagFuzzy\Models\SynonymGroups",
     *      inversedBy="synonyms",
     *      cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="synonymGroupId", referencedColumnName="id")
     */
    private $synonymGroup;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $name;

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
     * @return SynonymGroups
     */
    public function getSynonymGroup()
    {
        return $this->synonymGroup;
    }

    /**
     * @param SynonymGroups $synonymGroup
     *
     * @return $this
     */
    public function setSynonymGroup($synonymGroup)
    {
        $this->synonymGroup = $synonymGroup;

        return $this;
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
}
