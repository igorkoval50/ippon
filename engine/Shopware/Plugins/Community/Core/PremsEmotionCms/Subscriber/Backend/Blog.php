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

class Blog implements \Enlight\Event\SubscriberInterface {
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
      'Enlight_Controller_Action_PostDispatch_Backend_Blog' => 'onPostDispatchBackendBlog',
    );
  }

  /**
   * ExtJs auf Blog Detailseite im Backend um neuen Reiter "Einkaufswelten" erweitern
   * @param \Enlight_Event_EventArgs $args
   */
  public function onPostDispatchBackendBlog(\Enlight_Event_EventArgs $args) {
    $args->getSubject()->View()->addTemplateDir(
      $this->bootstrap->Path() . 'Views/'
    );
    $this->bootstrap->Application()->Snippets()->addConfigDir($this->bootstrap->Path() . 'Snippets/');

    // if the controller action name equals "load" we have to load all application components.
    if ($args->getRequest()->getActionName() === 'load') {
      $args->getSubject()->View()->extendsTemplate(
        'backend/blog/prems_emotion_cms/view/window.js'
      );
    }

    // if the controller action name equals "index" we have to extend the backend blog application
    if ($args->getRequest()->getActionName() === 'index') {
      $args->getSubject()->View()->extendsTemplate(
        'backend/blog/prems_emotion_cms/app.js'
      );
    }
  }
}