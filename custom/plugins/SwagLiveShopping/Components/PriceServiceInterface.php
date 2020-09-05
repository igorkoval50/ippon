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

namespace SwagLiveShopping\Components;

use Shopware\Bundle\StoreFrontBundle\Struct\Customer\Group;

interface PriceServiceInterface
{
    const MINUTES_A_DAY = 1440;

    const MINUTES_IN_HOUR = 60;

    /**
     * @throws NoLiveShoppingPriceException
     */
    public function getLiveShoppingPrice(
        $liveShoppingId,
        $liveShoppingType,
        \DateTime $buyTime,
        \DateTime $liveShoppingValidFrom,
        \DateTime $liveShoppingValidTo
    );

    public function applyLiveShoppingPrice(array $liveShopping, Group $customerGroup, bool $isTaxInput): array;

    public function getIsTaxInput(int $customerGroupId): ?int;

    public function getTaxRate(int $liveShoppingId): string;
}
