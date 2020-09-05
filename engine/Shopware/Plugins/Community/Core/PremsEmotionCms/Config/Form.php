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

class Form {
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

  /**
   * Returns all translations for the plugin configuration
   *
   * @return array
   */
  private function getFormTranslations() {
    return array(
      'en_GB' => array(
        'PremsEmotionCmsUseCaching' => array(
          'label' => 'Activate caching',
        ),
        'PremsEmotionCmsUseCachingCacheLifetime' => array(
          'label' => 'Cache lifetime',
        ),
        /**'PremsEmotionCmsNoAjax' => array(
          'label' => 'Load shopping worlds without ajax',
          'description' => 'Load plugin shopping wordls without ajax'
        ),*/
      ),
      'de_DE' => array(
        'PremsEmotionCmsUseCaching' => array(
          'label' => 'Caching aktivieren',
        ),
        'PremsEmotionCmsUseCachingCacheLifetime' => array(
          'label' => 'Cachezeit',
        ),
        /**'PremsEmotionCmsNoAjax' => array(
          'label' => 'Einkaufswelten ohne Ajax laden',
          'description' => 'Lädt die Plugin-Einkaufswelten ohne Ajax'
        ),*/
      )
    );
  }

  /**
   * Eingabefelder für Backend (Plugin-Einstellungen) setzen
   */
  public function setForm() {
    $form = $this->bootstrap->Form();

    /**$form->setElement('boolean', 'PremsEmotionCmsNoAjax', array(
      'label' => 'Load shopping worlds without ajax',
      'description' => 'Load plugin shopping wordls without ajax',
      'value' => 0,
      'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
    ));*/

    $form->setElement('boolean', 'PremsEmotionCmsUseCaching', array(
      'label' => 'Activate caching',
      'value' => 0,
      'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
    ));

    $form->setElement('number', 'PremsEmotionCmsUseCachingCacheLifetime', array(
      'label' => 'Cache lifetime',
      'value' => 86400,
      'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
    ));

    $this->bootstrap->addFormTranslations($this->getFormTranslations());
  }
}