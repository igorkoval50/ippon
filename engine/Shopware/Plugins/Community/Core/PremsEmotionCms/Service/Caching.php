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
namespace Shopware\PremsEmotionCms\Service;

use Shopware\Components\DependencyInjection\Container;

class Caching {
  /**
   * @var Container $container
   */
  protected $container;

  /** @var  \Shopware_Components_Config */
  protected $config;

  /**
   * @param Container $container
   * @param \Shopware_Components_Config $config
   */
  public function __construct(Container $container, $config) {
    $this->container = $container;
    $this->config = $config;
  }

  /**
   * @param $id
   * @return string
   */
  protected function generateKey($id) {
    $context  = $this->container->get('shopware_storefront.context_service')->getShopContext();
    $cacheKey = 'Prems_EmotionCms_' . $context->getShop()->getId() . '_' . $id;

    return $cacheKey;
  }

  /**
   * @param $id
   * @return bool|array
   */
  public function getCache($id) {
    if (!$this->config->get('PremsEmotionCmsUseCaching')) {
      return false;
    }
    $cacheKey = $this->generateKey($id);
    $cache = $this->container->get('cache');
    $emotions = $cache->load($cacheKey);

    return $emotions;
  }

  /**
   * @param $id
   * @param $emotions
   * @return bool
   */
  public function save($id, $emotions) {
    if (!$this->config->get('PremsEmotionCmsUseCaching')) {
      return false;
    }
    $cacheKey = $this->generateKey($id);
    $cache = $this->container->get('cache');
    $cache->save($emotions, $cacheKey, ['Shopware_Plugin'], (int) $this->config->get('PremsEmotionCmsUseCachingCacheLifetime', 86400));
  }
}