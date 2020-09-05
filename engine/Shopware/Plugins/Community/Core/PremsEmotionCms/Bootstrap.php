<?php
/**
 * premsoft
 * Copyright © 2016 Premsoft - Sven Mittreiter
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

use Doctrine\Common\Collections\ArrayCollection;
class Shopware_Plugins_Core_PremsEmotionCms_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
  public function getInfo() {
    return array(
      'version' => $this->getVersion(),
      'label' => $this->getLabel(),
      'autor' => 'PremSoft',
      'description' => '',
      'support' => 'Herstellerseite',
      'link' => 'http://www.premsoft.de'
    );
  }

  public function afterInit() {
    $this->Application()->Loader()->registerNamespace('Shopware\PremsEmotionCms', __DIR__ . DIRECTORY_SEPARATOR);
  }

  /**
   * Returns the current version of the plugin.
   * 1.1.0 Beim Duplizieren eines Artikels werden jetzt Einkaufswelten Zuordnungen übernommen.
   * 1.2.0 Es wurde die Möglichkeit eingebaut, mehreren Artikeln dieselben Einkaufswelten auf einmal zuzuweisen.
   * 1.2.1 Ein Problem wurde behoben was zu einer eingeschränkten Verfügbarkeit von Einkaufswelten in Kategorien führen konnte.
   * 1.2.2 Die Fullscreen Einkaufswelten Eigenschaft wird jetzt nicht mehr beachtet, so das diese verwendet werden können
   * 1.3.0 Einkaufswelten können jetzt auch in Blog Kategorien angezeigt werden (ab Shopware 5.4.0)
   * 1.3.1 Es wurde ein Problem behoben, das im Backend bei der Blog Übersicht die Shops nicht korrekt gezeigt wurden.
   * 1.4.0 Eine Stapelverarbeitung, mit der es möglich ist eine Einkaufswelt direkt vielen Artikel zuzuweisen, wurde eingebaut.
   * @return string
   */
  public function getVersion() {
    return '1.4.0';
  }

  /**
   * Get (nice) name for plugin manager list
   * @return string
   */
  public function getLabel() {
    return 'Einkaufswelten CMS';
  }

  public function getCapabilities() {
    return array(
      'install' => true,
      'update' => true,
      'enable' => true
    );
  }

  public function install() {
    $this->registerEvents();
    $this->createBackendMenu();
    $this->updateSchema();
    $this->createAttributes();

    $form = new \Shopware\PremsEmotionCms\Config\Form($this);
    $form->setForm();

    return array('success' => true, 'invalidateCache' => array('frontend', 'backend', 'proxy', 'config'));
  }

  /**
   * Prüft ob mindestes Shopware Version 5.2 vorliegt
   * @return bool
   */
  public function isShopware52() {
    return $this->assertMinimumVersion("5.2.0");
  }

  public function minimumVersion($requiredVersion) {
    $version = Shopware()->Config()->version;

    if ($version === '___VERSION___') {
      return true;
    }

    return version_compare($version, $requiredVersion, '>=');
  }

  /**
   * Prüft ob mindestes Shopware Version 5.3 vorliegt
   * @return bool
   */
  public function isShopware53() {
    return $this->assertMinimumVersion("5.3.0");
  }

  protected function createAttributes() {
    //$shopwareAttributeCompability = new \Shopware\PremsCoupon\Config\Compability\Attribute($this);
    //$shopwareAttributeCompability->createAttributes();
  }

  public function update($version) {
    $update = new \Shopware\PremsEmotionCms\Config\Update($this);
    $update->execute($version);
    return array('success' => true, 'invalidateCache' => array('frontend', 'backend', 'proxy', 'config'));
  }

  public function enable() {
    return array('success' => true, 'invalidateCache' => array('frontend', 'backend', 'proxy', 'config'));
  }

  protected function createBackendMenu() {
    $menu = new \Shopware\PremsEmotionCms\Config\Menu($this);
    $menu->create();
  }

  protected function updateSchema() {
    $this->registerCustomModels();

    $em = $this->Application()->Models();
    $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

    /**$classes = array(
      $em->getClassMetadata('Shopware\CustomModels\PremsEmotionCms\Article'),
      $em->getClassMetadata('Shopware\CustomModels\PremsEmotionCms\Article2'),
      $em->getClassMetadata('Shopware\CustomModels\PremsEmotionCms\Blog'),
      $em->getClassMetadata('Shopware\CustomModels\PremsEmotionCms\Site'),
      $em->getClassMetadata('Shopware\CustomModels\PremsEmotionCms\Supplier'),
    );*/

    try {
      //$tool->dropSchema($classes);
      $tool->createSchema(array($em->getClassMetadata('Shopware\CustomModels\PremsEmotionCms\Article')));
    } catch (\Exception $e) {
      //ignore
    }
    try {
      //$tool->dropSchema($classes);
      $tool->createSchema(array($em->getClassMetadata('Shopware\CustomModels\PremsEmotionCms\Article2')));
    } catch (\Exception $e) {
      //ignore
    }
    try {
      //$tool->dropSchema($classes);
      $tool->createSchema(array($em->getClassMetadata('Shopware\CustomModels\PremsEmotionCms\Blog')));
    } catch (\Exception $e) {
      //ignore
    }
    try {
      //$tool->dropSchema($classes);
      $tool->createSchema(array($em->getClassMetadata('Shopware\CustomModels\PremsEmotionCms\Site')));
    } catch (\Exception $e) {
      //ignore
    }
    try {
      //$tool->dropSchema($classes);
      $tool->createSchema(array($em->getClassMetadata('Shopware\CustomModels\PremsEmotionCms\Supplier')));
    } catch (\Exception $e) {
      //ignore
    }
    //$tool->createSchema($classes);
  }

  public function onCollectLessFiles() {
    $lessDir = __DIR__ . '/Views/prems_emotion_cms/frontend/_resources/src/less/';

    $less = new \Shopware\Components\Theme\LessDefinition(
      array(),
      array(
        $lessDir . 'premsemotioncms.less'
      )
    );

    return new ArrayCollection(array($less));
  }

  /**
   * @return ArrayCollection
   */
  public function onCollectJavascriptFiles() {
    $jsDir = __DIR__ . '/Views/prems_emotion_cms/frontend/_resources/src/js/';

    $jsLibs = array(
      $jsDir . 'jquery.premsemotioncms.js',
    );

    return new ArrayCollection($jsLibs);
  }

  /**
   * Registrieren der Subscriber
   * @param Enlight_Event_EventArgs $args
   */
  public function onRegisterSubscriber(Enlight_Event_EventArgs $args) {
    $container = $this->collection->Application()->Container();

    $this->registerCustomModels();

    $subscriber = new \Shopware\PremsEmotionCms\Config\Subscriber($this);
    $subscriber->add();


  }

  public function registerEvents() {
    $this->subscribeEvent('Enlight_Controller_Front_StartDispatch', 'onRegisterSubscriber');
    $this->subscribeEvent('Shopware_Console_Add_Command', 'onRegisterSubscriber');

    $this->subscribeEvent(
      'Theme_Compiler_Collect_Plugin_Less',
      'onCollectLessFiles'
    );

    $this->subscribeEvent(
      'Theme_Compiler_Collect_Plugin_Javascript',
      'onCollectJavascriptFiles'
    );
  }

  public function uninstall() {
    return array('success' => true, 'invalidateCache' => array('frontend'));
  }

  public function addTemplateAndSnippetDir() {
    $this->Application()->Template()->addTemplateDir(
      $this->Path() . 'Views/prems_emotion_cms/'
    );

    $this->Application()->Snippets()->addConfigDir($this->Path() . 'Snippets/');
  }
}