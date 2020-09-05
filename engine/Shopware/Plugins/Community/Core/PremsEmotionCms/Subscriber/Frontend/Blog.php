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
namespace Shopware\PremsEmotionCms\Subscriber\Frontend;

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
      'Enlight_Controller_Action_PostDispatchSecure_Frontend_Blog' => 'onFrontendPostDispatch',
    );
  }

  /**
   * Frontend Event welches die Einkaufswelten auf einer Blogseite hinzufügt
   * @param \Enlight_Event_EventArgs $arguments
   */
  public function onFrontendPostDispatch(\Enlight_Event_EventArgs $arguments) {
    $subject = $arguments->getSubject();
    $request  = $subject->Request();
    $response = $subject->Response();
    $action = $request->getActionName();
    $view = $subject->View();

    if ($action == 'index' && $this->bootstrap->minimumVersion("5.4.0")) {
      $this->bootstrap->addTemplateAndSnippetDir();
      $categoryId = $request->getParam('sCategory');
      $context = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();
      $service = Shopware()->Container()->get('shopware_emotion.store_front_emotion_device_configuration');
      $emotions = $service->getCategoryConfiguration($categoryId, $context, false);

      $view->assign('emotions', $emotions);
      $view->assign('hasEmotion', !empty($emotions));

    } elseif ($action == 'detail') {
      $this->bootstrap->addTemplateAndSnippetDir();
      $id = (int)$request->getQuery('blogArticle');
      $emotionService = Shopware()->Container()->get('prems_emotion_cms.blog');
      $emotions = $emotionService->getEmotions($id);
      $view->assign('PremsEmotionCmsNoAjax', Shopware()->Config()->get('PremsEmotionCmsNoAjax'));
      $view->assign('emotions', $emotions);
    } else {
      return;
    }
  }
}