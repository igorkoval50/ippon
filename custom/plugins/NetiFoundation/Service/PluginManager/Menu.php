<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\PluginManager;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use NetiFoundation\Service\ApplicationInterface;
use NetiFoundation\Service\Logging\LoggingServiceInterface;
use NetiFoundation\Struct\PluginConfigFile\MenuEntry;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\MenuSynchronizer;
use Shopware\Models\Plugin\Plugin as PluginModel;

/**
 * Class Menu
 *
 * @package NetiFoundation\Service\PluginManager
 *
 * @deprecated 4.0.0 - Use Resources/menu.xml instead
 * @see        https://developers.shopware.com/developers-guide/plugin-quick-start/#backend-menu-items
 */
class Menu implements MenuInterface
{
    /**
     * @var ModelManager
     */
    protected $em;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var LoggingServiceInterface
     */
    protected $loggingService;

    /**
     * @param ModelManager            $em
     * @param Connection              $connection
     * @param LoggingServiceInterface $loggingService
     */
    public function __construct(
        ModelManager $em,
        Connection $connection,
        LoggingServiceInterface $loggingService
    ) {
        $this->em                = $em;
        $this->connection        = $connection;
        $this->loggingService    = $loggingService;
    }

    /**
     * @param Plugin      $plugin
     * @param MenuEntry[] $menuEntries
     *
     * @deprecated 4.0.0 - Use Resources/menu.xml instead
     * @see        https://developers.shopware.com/developers-guide/plugin-quick-start/#backend-menu-items
     */
    public function installMenu(Plugin $plugin, array $menuEntries)
    {
        \trigger_error(\sprintf(
            '%s is deprecated since 4.0.0. Use Resources/menu.xml file instead.',
            __METHOD__
        ), \E_USER_DEPRECATED);

        $logs        = ['success' => true, 'items' => []];
        try {
            $newMenu = [];

            foreach ($menuEntries as $entry) {
                $label = $entry->getLabel();
                $name  = $label->getDe();

                $log = [
                    'name'    => $name,
                    'success' => null // Shopware handles the menu synchronization, so the status is unavailable
                ];

                $children  = $entry->getChildren();
                $newMenu[] = [
                    'isRootMenu' => false,
                    'name'       => $name,
                    'label'      => $label->toArray(),
                    'controller' => $entry->getController(),
                    'action'     => $entry->getAction(),
                    'onclick'    => $entry->getOnclick(),
                    'class'      => $entry->getClass(),
                    'active'     => $entry->getActive(),
                    'position'   => $entry->getPosition(),
                    'children'   => $children,
                    'parent'     => $entry->getParent(),
                ];

                $logs['items'][] = $log;
            }

            $pluginModel = $this->em
                ->getRepository(PluginModel::class)
                ->findOneBy(['name' => $plugin->getName()]);

            $menuSynchronizer = new MenuSynchronizer($this->em);
            $menuSynchronizer->synchronize($pluginModel, $newMenu);
        } catch (\Exception $e) {
            $logs['success'] = false;
            $logs['message'] = 'Error ' . $e->getCode() . ': ' . $e->getMessage();
        }

        $this->loggingService->write(
            $plugin->getName(),
            __FUNCTION__,
            $logs['success'] ? 'Successful' : 'Error',
            ['menu' => $logs]
        );
    }

    /**
     * @param Plugin $plugin
     *
     * @throws DBALException
     *
     * @deprecated 4.0.0 - Use Resources/menu.xml instead
     * @see        https://developers.shopware.com/developers-guide/plugin-quick-start/#backend-menu-items
     */
    public function removeMenu(Plugin $plugin)
    {
        \trigger_error(\sprintf(
            '%s is deprecated since 4.0.0. Use Resources/menu.xml file instead.',
            __METHOD__
        ), \E_USER_DEPRECATED);

        $sql = <<<'SQL'
DELETE `scm` FROM `s_core_menu` `scm`
INNER JOIN `s_core_plugins` `scp` ON `scm`.`pluginid` = `scp`.`id`
WHERE `scp`.`name` = :pluginName
SQL;
        $this->connection->executeUpdate($sql, [':pluginName' => $plugin->getName()]);
    }
}
