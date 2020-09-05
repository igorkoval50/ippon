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

namespace SwagNewsletter\Subscriber;

use Enlight\Event\SubscriberInterface;

class NewsletterRepository implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware\Models\Newsletter\Repository::getListNewslettersQueryBuilder::after' => 'afterGetListNewsletterQueryBuilder',
        ];
    }

    /**
     * Removes all unnecessary data for the listing of the newsletter-page.
     * This data will be loaded on edit instead.
     *
     * @param \Enlight_Hook_HookArgs $arguments
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function afterGetListNewsletterQueryBuilder(\Enlight_Hook_HookArgs $arguments)
    {
        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $arguments->getReturn();

        $queryBuilder->resetDQLParts([
            'select',
            'join',
        ]);

        $queryBuilder->select('mailing');

        return $queryBuilder;
    }
}
