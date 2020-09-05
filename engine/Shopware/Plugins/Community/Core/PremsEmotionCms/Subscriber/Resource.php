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
 * @copyright  Copyright (c) 2017, premsoft - Sven Mittreiter (http://www.premsoft.de)
 * @author     Sven Mittreiter <info@premsoft.de>
 */
namespace Shopware\PremsEmotionCms\Subscriber;

use Enlight\Event\SubscriberInterface;

use Shopware\PremsEmotionCms\Service\Article;
use Shopware\PremsEmotionCms\Service\Blog;
use Shopware\PremsEmotionCms\Service\Caching;
use Shopware\PremsEmotionCms\Service\Site;
use Shopware\PremsEmotionCms\Service\EmotionConverter;
use Shopware\PremsEmotionCms\Service\Supplier;
use Shopware\Components\DependencyInjection\Container;
use Shopware\PremsEmotionCms\Service\ArticleBatch;
use Shopware\PremsEmotionCms\Service\ArticleBatchData;
use Shopware_Plugins_Core_PremsEmotionCms_Bootstrap as Bootstrap;

class Resource implements SubscriberInterface {

  /** @var  Container */
  protected $container;

  /** @var  \Shopware_Components_Config */
  protected $config;

  /**
   * @var Bootstrap $bootstrap
   */
  protected $bootstrap;

  public static function getSubscribedEvents() {
      return [
        'Enlight_Bootstrap_InitResource_prems_emotion_cms.article' => 'onArticleService',
        'Enlight_Bootstrap_InitResource_prems_emotion_cms.blog' => 'onBlogService',
        'Enlight_Bootstrap_InitResource_prems_emotion_cms.caching' => 'onCachingService',
        'Enlight_Bootstrap_InitResource_prems_emotion_cms.site' => 'onSiteService',
        'Enlight_Bootstrap_InitResource_prems_emotion_cms.emotion_converter' => 'onEmotionConverterService',
        'Enlight_Bootstrap_InitResource_prems_emotion_cms.supplier' => 'onSupplierService',
        'Enlight_Bootstrap_InitResource_prems_emotion_cms.article_batch' => 'onBatchService',
        'Enlight_Bootstrap_InitResource_prems_emotion_cms.article_batch_data' => 'onBatchDataService',
      ];
  }

  public function __construct(Container $container, $config, $bootstrap) {
    $this->container = $container;
    $this->config = $config;
    $this->bootstrap = $bootstrap;
  }

  public function onBatchService() {
    $batchService = new ArticleBatch($this->bootstrap, $this->container->get('prems_emotion_cms.article_batch_data'));
    return $batchService;
  }

  public function onBatchDataService() {
    $batchDataService = new ArticleBatchData($this->bootstrap);
    return $batchDataService;
  }

  public function onArticleService() {
    return new Article(
      Shopware()->Db(), Shopware()->Shop(),
      $this->container->get('prems_emotion_cms.caching'),
      $this->container->get('prems_emotion_cms.emotion_converter'));
  }

  public function onBlogService() {
    return new Blog(
      Shopware()->Db(), Shopware()->Shop(),
      $this->container->get('prems_emotion_cms.caching'),
      $this->container->get('prems_emotion_cms.emotion_converter'));
  }

  public function onCachingService() {
    return new Caching($this->container, $this->config);
  }

  public function onSiteService() {
    return new Site(
      Shopware()->Db(), Shopware()->Shop(),
      $this->container->get('prems_emotion_cms.caching'),
      $this->container->get('prems_emotion_cms.emotion_converter'));
  }

  public function onEmotionConverterService() {
    return new EmotionConverter($this->bootstrap);
  }

  public function onSupplierService() {
    return new Supplier(
      Shopware()->Db(), Shopware()->Shop(),
      $this->container->get('prems_emotion_cms.caching'),
      $this->container->get('prems_emotion_cms.emotion_converter'));
  }
}
