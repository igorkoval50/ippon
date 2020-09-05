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
class Shopware_Controllers_Backend_PremsEmotionCmsArticle2 extends Shopware_Controllers_Backend_Application  {
  protected $model = 'Shopware\CustomModels\PremsEmotionCms\Article2';
  protected $alias = 'article';

  protected function getDetailQuery($id) {
    $builder = parent::getDetailQuery($id);

    $builder->leftJoin('article.emotions', 'emotions')
      ->leftJoin('article.articles', 'articles');


    $builder->addSelect(array('emotions', 'articles'));

    return $builder;
  }
}