<?php
/**
 * premsoft
 * Copyright © 2018 Premsoft - Sven Mittreiter
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License, supplemented by an additional
 * permission, and of our proprietary license can be found
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, titles and interests in the
 * above trademarks remain entirely with the trademark owners.
 *
 * @copyright  Copyright (c) 2018, premsoft - Sven Mittreiter (http://www.premsoft.de)
 * @author     Sven Mittreiter <info@premsoft.de>
 */

namespace Shopware\CustomModels\PremsEmotionCms;

use Shopware\Components\Model\ModelEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Model für die Mehrfachzuordnung von Artikeln und Einkaufswelten
 * @ORM\Entity
 * @ORM\Table(name="s_prems_emotion_cms_article2")
 */
class Article2 extends ModelEntity
{
  public function __construct()
  {
    $this->emotions = new ArrayCollection();
    $this->articles = new ArrayCollection();
  }


  /**
   * @var integer $id
   *
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  protected $id;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=100, nullable=false)
   */
  protected $name;

  /**
   * @var
   * @ORM\Column(name="position", type="integer")
   */
  protected $position;

  /**
   * Variable um festzulegen ob die Inhalte vor oder nach den Einkaufswelten erscheinen sollen die über das reguläre
   * Artikel Tab zugewiesen wurden.
   * @var
   * @ORM\Column(name="before_default", type="integer", nullable=true)
   */
  protected $beforeDefault;

  /**
   * @var ArrayCollection
   *
   * @ORM\ManyToMany(targetEntity="\Shopware\Models\Emotion\Emotion")
   * @ORM\JoinTable(name="s_prems_emotion_cms_article2_emotions",
   *      joinColumns={
   *          @ORM\JoinColumn(
   *              name="main_id",
   *              referencedColumnName="id"
   *          )
   *      },
   *      inverseJoinColumns={
   *          @ORM\JoinColumn(
   *              name="emotion_id",
   *              referencedColumnName="id"
   *          )
   *      }
   * )
   */
  protected $emotions;

  /**
   * @var ArrayCollection
   *
   * @ORM\ManyToMany(targetEntity="\Shopware\Models\Article\Article")
   * @ORM\JoinTable(name="s_prems_emotion_cms_article2_articles",
   *      joinColumns={
   *          @ORM\JoinColumn(
   *              name="main_id",
   *              referencedColumnName="id"
   *          )
   *      },
   *      inverseJoinColumns={
   *          @ORM\JoinColumn(
   *              name="article_id",
   *              referencedColumnName="id"
   *          )
   *      }
   * )
   */
  protected $articles;

  /**
   * @param float $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return float
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @return mixed
   */
  public function getPosition()
  {
    return $this->position;
  }

  /**
   * @param mixed $position
   */
  public function setPosition($position)
  {
    $this->position = $position;
  }

  /**
   * @param \Doctrine\Common\Collections\ArrayCollection $emotions
   */
  public function setEmotions($emotions)
  {
    $this->emotions = $emotions;
  }

  /**
   * @return \Doctrine\Common\Collections\ArrayCollection
   */
  public function getEmotions()
  {
    return $this->emotions;
  }

  /**
   * @return ArrayCollection
   */
  public function getArticles()
  {
    return $this->articles;
  }

  /**
   * @param ArrayCollection $articles
   */
  public function setArticles($articles)
  {
    $this->articles = $articles;
  }

  /**
   * @return mixed
   */
  public function getBeforeDefault()
  {
    return $this->beforeDefault;
  }

  /**
   * @param mixed $beforeDefault
   */
  public function setBeforeDefault($beforeDefault)
  {
    $this->beforeDefault = $beforeDefault;
  }
}