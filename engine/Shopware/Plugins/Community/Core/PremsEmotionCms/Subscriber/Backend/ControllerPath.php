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
namespace Shopware\PremsEmotionCms\Subscriber\Backend;

use Shopware_Plugins_Core_PremsEmotionCms_Bootstrap as Bootstrap;

use Enlight\Event\SubscriberInterface;

class ControllerPath implements SubscriberInterface {
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

  public static function getSubscribedEvents() {
    return array(
      'Enlight_Controller_Dispatcher_ControllerPath_Backend_PremsEmotionCmsEmotion' => 'onGetBackendControllerEmotion',
      'Enlight_Controller_Dispatcher_ControllerPath_Backend_PremsEmotionCmsArticle' => 'onGetBackendControllerArticle',
      'Enlight_Controller_Dispatcher_ControllerPath_Backend_PremsEmotionCmsArticle2' => 'onGetBackendControllerArticle2',
      'Enlight_Controller_Dispatcher_ControllerPath_Backend_PremsEmotionCmsBlog' => 'onGetBackendControllerBlog',
      'Enlight_Controller_Dispatcher_ControllerPath_Backend_PremsEmotionCmsSite' => 'onGetBackendControllerSite',
      'Enlight_Controller_Dispatcher_ControllerPath_Backend_PremsEmotionCmsSupplier' => 'onGetBackendControllerSupplier',
    );
  }

  /**
   * @param \Enlight_Event_EventArgs $args
   * @return string
   */
  public function onGetBackendControllerEmotion(\Enlight_Event_EventArgs $args) {

    return $this->bootstrap->Path() . '/Controllers/Backend/PremsEmotionCmsEmotion.php';
  }

  /**
   * @param \Enlight_Event_EventArgs $args
   * @return string
   */
  public function onGetBackendControllerArticle(\Enlight_Event_EventArgs $args) {

    $this->bootstrap->Application()->Template()->addTemplateDir(
      $this->bootstrap->Path() . 'Views/'
    );

    $this->bootstrap->Application()->Snippets()->addConfigDir($this->bootstrap->Path() . 'Snippets/');

    return $this->bootstrap->Path() . '/Controllers/Backend/PremsEmotionCmsArticle.php';
  }

  /**
   * @param \Enlight_Event_EventArgs $args
   * @return string
   */
  public function onGetBackendControllerArticle2(\Enlight_Event_EventArgs $args) {

    $this->bootstrap->Application()->Template()->addTemplateDir(
      $this->bootstrap->Path() . 'Views/'
    );

    $this->bootstrap->Application()->Snippets()->addConfigDir($this->bootstrap->Path() . 'Snippets/');

    return $this->bootstrap->Path() . '/Controllers/Backend/PremsEmotionCmsArticle2.php';
  }

  /**
   * @param \Enlight_Event_EventArgs $args
   * @return string
   */
  public function onGetBackendControllerBlog(\Enlight_Event_EventArgs $args) {

    $this->bootstrap->Application()->Template()->addTemplateDir(
      $this->bootstrap->Path() . 'Views/'
    );

    $this->bootstrap->Application()->Snippets()->addConfigDir($this->bootstrap->Path() . 'Snippets/');

    return $this->bootstrap->Path() . '/Controllers/Backend/PremsEmotionCmsBlog.php';
  }


  /**
   * @param \Enlight_Event_EventArgs $args
   * @return string
   */
  public function onGetBackendControllerSite(\Enlight_Event_EventArgs $args) {

    $this->bootstrap->Application()->Template()->addTemplateDir(
      $this->bootstrap->Path() . 'Views/'
    );

    $this->bootstrap->Application()->Snippets()->addConfigDir($this->bootstrap->Path() . 'Snippets/');

    return $this->bootstrap->Path() . '/Controllers/Backend/PremsEmotionCmsSite.php';
  }

  /**
   * @param \Enlight_Event_EventArgs $args
   * @return string
   */
  public function onGetBackendControllerSupplier(\Enlight_Event_EventArgs $args) {

    $this->bootstrap->Application()->Template()->addTemplateDir(
      $this->bootstrap->Path() . 'Views/'
    );

    $this->bootstrap->Application()->Snippets()->addConfigDir($this->bootstrap->Path() . 'Snippets/');

    return $this->bootstrap->Path() . '/Controllers/Backend/PremsEmotionCmsSupplier.php';
  }
}