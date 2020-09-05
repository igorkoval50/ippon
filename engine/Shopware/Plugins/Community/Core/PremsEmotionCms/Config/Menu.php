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
namespace Shopware\PremsEmotionCms\Config;

use Shopware_Plugins_Core_PremsEmotionCms_Bootstrap as Bootstrap;

class Menu {
  /**
   * @var Bootstrap $bootstrap
   */
  protected $bootstrap;

  /**
   * @param Bootstrap $bootstrap
   */
  public function __construct(Bootstrap $bootstrap) {
    $this->bootstrap = $bootstrap;
  }

  public function create() {
    $rootId =  $this->bootstrap->createMenuItem(array(
      'label' => 'Einkaufswelten CMS',
      'controller' => 'PremsEmotionCms',
      'class' => 'prems-icon',
      'action' => null,
      'active' => 1,
      'parent' => $this->bootstrap->Menu()->findOneBy(array('label' => 'Marketing'))
    ));

    Shopware()->Models()->flush();

    $this->bootstrap->createMenuItem(array(
      'label' => 'Einkaufswelten auf Shopseiten',
      'controller' => 'PremsEmotionCmsSite',
      'action' => 'Index',
      'class' => 'prems-icon',
      'active' => 1,
      'parent' => $this->bootstrap->Menu()->findOneBy(array('id' => $rootId->getId()))
    ));

    $this->bootstrap->createMenuItem(array(
      'label' => 'Einkaufswelten auf Herstellerseiten',
      'controller' => 'PremsEmotionCmsSupplier',
      'action' => 'Index',
      'class' => 'prems-icon',
      'active' => 1,
      'parent' => $this->bootstrap->Menu()->findOneBy(array('id' => $rootId->getId()))
    ));

    $this->createArticle2CmsMenu($this->bootstrap->Menu()->findOneBy(array('id' => $rootId->getId())));
  }

  public function createArticle2CmsMenu($parentId) {
    $this->bootstrap->createMenuItem(array(
      'label' => 'Einkaufswelten auf Artikelseiten',
      'controller' => 'PremsEmotionCmsArticle2',
      'action' => 'Index',
      'class' => 'prems-icon',
      'active' => 1,
      'parent' => $parentId
    ));
  }
}