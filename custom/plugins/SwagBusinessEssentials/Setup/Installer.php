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

namespace SwagBusinessEssentials\Setup;

use DateTime;
use RuntimeException;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Models\Mail\Mail;
use Shopware\Models\Menu\Menu;
use Shopware\Models\Shop\Shop;
use SwagBusinessEssentials\Setup\Migration\TableFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Installer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Creates the necessary database-tables and adds example email-data to the system.
     */
    public function install()
    {
        if ($this->isTableStillPresent()) {
            $this->executeMigration();

            return;
        }

        $this->createDatabaseTables();
        $this->addEmailDemoData();
    }

    public function update(UpdateContext $updateContext)
    {
        if (version_compare($updateContext->getCurrentVersion(), '2.0.2', '<')) {
            $sql = 'ALTER TABLE `s_core_plugins_b2b_private`
                    ADD `whitelistedcontrollers` VARCHAR(255) NULL AFTER `redirectregistration`';
            $this->container->get('db')->query($sql);
        }

        if (version_compare($updateContext->getCurrentVersion(), '2.1.7', '<')) {
            $this->addEmailDemoData();

            $sql = 'ALTER TABLE `s_core_plugins_b2b_private` ADD `redirectURL` VARCHAR(1024)';
            $this->container->get('db')->query($sql);
        }

        if (version_compare($updateContext->getCurrentVersion(), '3.0.0', '<')) {
            $this->executeMigration();
            $this->removeOldMenuEntry();
        }

        // If the broken 3.0.0 was installed and the migration was not triggered already, we check it here again.
        if ($updateContext->getCurrentVersion() === '3.0.0' && $this->isTableStillPresent()) {
            $this->executeMigration();
            $this->removeOldMenuEntry();
        }
    }

    /**
     * Removes all created database-tables, that were created in this plugin.
     *
     * @throws RuntimeException
     */
    public function uninstall()
    {
        $dbalConnection = $this->container->get('dbal_connection');
        $tableFactory = new TableFactory($dbalConnection, $this->container->get('events'));
        $tables = $tableFactory->factory();

        $dbalConnection->exec('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($tables as $table) {
            if (!$table->drop()) {
                $dbalConnection->exec('SET FOREIGN_KEY_CHECKS = 1;');
                throw new RuntimeException(sprintf('Could not drop table %s', $table->getTableName()));
            }
        }

        $dbalConnection->exec('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * @throws RuntimeException
     */
    public function createDatabaseTables()
    {
        $tableFactory = new TableFactory($this->container->get('dbal_connection'), $this->container->get('events'));
        foreach ($tableFactory->factory() as $table) {
            if (!$table->install()) {
                throw new RuntimeException(sprintf('Could not install table %s', $table->getTableName()));
            }
        }
    }

    /**
     * Add demo data for accept and reject emails.
     */
    private function addEmailDemoData()
    {
        $mailsTemplateName = [
            'sCUSTOMERGROUPHACCEPTED',
            'sCUSTOMERGROUPHREJECTED',
        ];

        /** @var ModelManager $modelManager */
        $modelManager = $shop = $this->container->get('models');

        /** @var Shop $shop */
        $shop = $modelManager->getRepository(Shop::class)->getActiveDefault();
        $defaultContext = [
            'sShop' => $this->container->get('config')->get('ShopName'),
            'sShopURL' => 'http://' . $shop->getHost() . $shop->getBasePath(),
        ];

        $dateTime = new DateTime('now');

        $mailDemoData = [
            'customerId' => 1,
            'countryId' => 2,
            'country' => 'Deutschland',
            'stateId' => 7,
            'company' => '',
            'department' => '',
            'salutation' => 'Herr',
            'number' => '20003',
            'firstName' => 'Max',
            'lastName' => 'Mustermann',
            'street' => 'Musterstraße 123',
            'zipCode' => '55555',
            'city' => 'Musterhausen',
            'phone' => '0123498765',
            'vatId' => '',
            'birthday' => $dateTime->format('Y-m-d'),
            'id' => 1,
            'fax' => '',
            'customer' => [
                'id' => 1,
            ],
        ];

        foreach ($mailDemoData as $key => $value) {
            $defaultContext[$key] = $value;
        }

        foreach ($mailsTemplateName as $mailTemplateName) {
            $mailTemplate = $modelManager->getRepository(Mail::class)->findOneBy(['name' => $mailTemplateName]);
            if ($mailTemplate === null) {
                continue;
            }

            $mailTemplate->setContext($defaultContext);
            $modelManager->persist($mailTemplate);
        }

        $modelManager->flush();
    }

    /**
     * Removes the old menu-entry, if any available.
     */
    private function removeOldMenuEntry()
    {
        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');

        $menuEntry = $modelManager->getRepository(Menu::class)->findOneBy([
            'controller' => 'BusinessEssentials',
        ]);

        if (!$menuEntry) {
            return;
        }

        $modelManager->remove($menuEntry);
        $modelManager->flush();
    }

    /**
     * Checks if an old table from a previous business essentials version is still present.
     *
     * @return bool
     */
    private function isTableStillPresent()
    {
        /** @var \Doctrine\DBAL\Connection $dbal */
        $dbal = $this->container->get('dbal_connection');
        $table = $dbal->fetchColumn("SHOW TABLES LIKE 's_core_plugins_b2b_tpl_config'");

        if (!$table) {
            return false;
        }

        $column = $dbal->fetchColumn("SHOW COLUMNS FROM `s_core_plugins_b2b_tpl_config` LIKE 'fieldkey'");

        if ($column) {
            return true;
        }

        return false;
    }

    private function executeMigration()
    {
        $migration = new Migration(
            $this->container->get('dbal_connection'),
            $this->container->get('events')
        );

        $migration->migrate();
    }
}
