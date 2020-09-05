<?php
/**
 * premsoft
 * Copyright © 2017 Premsoft - Sven Mittreiter
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
 * @copyright  Copyright (c) 2015, premsoft - Sven Mittreiter (http://www.premsoft.de)
 * @author     Sven Mittreiter <info@premsoft.de>
 */
namespace Shopware\PremsEmotionCms\Config;

use Shopware_Plugins_Core_PremsEmotionCms_Bootstrap as Bootstrap;

class Subscriber
{
  /**
   * @var Bootstrap $bootstrap
   */
  protected $bootstrap;

  /**
   * @param Bootstrap $bootstrap
   */
  public function __construct(Bootstrap $bootstrap)
  {
    $this->bootstrap = $bootstrap;
  }

  public function add() {
    $subscribers = array(
      new \Shopware\PremsEmotionCms\Subscriber\Resource(Shopware()->Container(), Shopware()->Config(), $this->bootstrap),
      new \Shopware\PremsEmotionCms\Subscriber\Backend\ControllerPath($this->bootstrap),
      new \Shopware\PremsEmotionCms\Subscriber\Backend\Setup($this->bootstrap),
      new \Shopware\PremsEmotionCms\Subscriber\Backend\Article($this->bootstrap),
      new \Shopware\PremsEmotionCms\Subscriber\Backend\Blog($this->bootstrap),

      new \Shopware\PremsEmotionCms\Subscriber\Frontend\Article($this->bootstrap),
      new \Shopware\PremsEmotionCms\Subscriber\Frontend\Blog($this->bootstrap),
      new \Shopware\PremsEmotionCms\Subscriber\Frontend\Supplier($this->bootstrap),
      new \Shopware\PremsEmotionCms\Subscriber\Frontend\Custom($this->bootstrap),

      new \Shopware\PremsEmotionCms\Subscriber\Widgets\Emotion($this->bootstrap),
    );

    foreach ($subscribers as $subscriber) {
      $this->bootstrap->Application()->Events()->addSubscriber($subscriber);
    }
  }
}