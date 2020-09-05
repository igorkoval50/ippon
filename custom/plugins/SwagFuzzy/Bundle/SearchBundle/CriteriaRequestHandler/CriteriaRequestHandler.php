<?php
/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace SwagFuzzy\Bundle\SearchBundle\CriteriaRequestHandler;

use Enlight_Controller_Request_RequestHttp as Request;
use Shopware\Bundle\SearchBundle\Condition\SearchTermCondition;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\CriteriaRequestHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use SwagFuzzy\Bundle\SearchBundle\Facet\KeywordFacet;

/**
 * Class CriteriaRequestHandler
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class CriteriaRequestHandler implements CriteriaRequestHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest(Request $request, Criteria $criteria, ShopContextInterface $context)
    {
        if (!$criteria->hasCondition('search')) {
            return;
        }

        /** @var $condition SearchTermCondition */
        $condition = $criteria->getCondition('search');

        $criteria->addFacet(
            new KeywordFacet($condition->getTerm())
        );
    }
}
