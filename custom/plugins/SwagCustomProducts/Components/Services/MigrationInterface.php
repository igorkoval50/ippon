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

namespace SwagCustomProducts\Components\Services;

/**
 * This interface is for easy extending or overwriting the Migration service
 */
interface MigrationInterface
{
    /**
     * Checks if a migration from the old "Customizing" plugin is possible by checking
     * if old plugin and its tables exist
     *
     * @return bool
     */
    public function isMigrationPossible();

    /**
     * Returns the groups which can be migrated
     *
     * @return array | null
     */
    public function getGroups();

    /**
     * Saves flag whether migration button is active or not
     *
     * @param bool $value
     *
     * @return bool|\Doctrine\DBAL\Driver\Statement|int
     */
    public function saveHideMigrationButton($value);

    /**
     * Starts the migration for the given group ID
     *
     * @param int | string $groupId
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function startMigration($groupId);

    /**
     * Returns an array of all occured errors
     *
     * @return array
     */
    public function getErrorLog();
}
