<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\PluginManager;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use NetiFoundation\Service\Logging\LoggingServiceInterface;
use NetiFoundation\Struct\PluginConfigFile\CronJob;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Models\Plugin\Plugin as PluginModel;

/**
 * Class Cron
 *
 * @package NetiFoundation\Service\PluginManager
 *
 * @deprecated 4.0.0 - use Resources/cronjob.xml instead
 * @see        https://developers.shopware.com/developers-guide/plugin-quick-start/#plugin-cronjob
 */
class Cron implements CronInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var LoggingServiceInterface
     */
    protected $loggingService;

    /**
     * @var ModelManager
     */
    private $em;

    /**
     * @param Connection              $connection
     * @param LoggingServiceInterface $loggingService
     * @param ModelManager            $em
     */
    public function __construct(
        Connection $connection,
        LoggingServiceInterface $loggingService,
        ModelManager $em
    ) {
        $this->connection     = $connection;
        $this->loggingService = $loggingService;
        $this->em             = $em;
    }

    /**
     * @param Plugin    $plugin
     * @param CronJob[] $cronJobs
     *
     * @throws DBALException
     *
     * @deprecated 4.0.0 - use Resources/cronjob.xml instead
     * @see        https://developers.shopware.com/developers-guide/plugin-quick-start/#plugin-cronjob
     */
    public function addCronJobs(Plugin $plugin, array $cronJobs)
    {
        $this->updateCronJobs($plugin, $cronJobs);
    }

    /**
     * @param Plugin    $plugin
     * @param CronJob[] $cronJobs
     *
     * @throws DBALException
     *
     * @deprecated 4.0.0 - use Resources/cronjob.xml instead
     * @see        https://developers.shopware.com/developers-guide/plugin-quick-start/#plugin-cronjob
     */
    public function updateCronJobs(Plugin $plugin, array $cronJobs)
    {
        \trigger_error(sprintf(
            '%s is deprecated since Foundation 4.0.0, please use the Resource/cronjob.xml file instead.',
            __METHOD__
        ), \E_USER_DEPRECATED);

        $logs = ['success' => true, 'cronjobs' => []];

        foreach ($cronJobs as $cronJob) {
            $log = array(
                'job' => $cronJob->getName(),
                'action' => $cronJob->getAction(),
                'success' => false
            );

            // on update the cronjob will already exist, so skip the insert
            if (! $this->cronJobExists($cronJob->getAction())) {
                $insertID = $this->addCronJob(
                    $plugin,
                    $cronJob->getName(),
                    $cronJob->getAction(),
                    $cronJob->getInterval(),
                    $cronJob->getActive()
                );

                if ($insertID) {
                    // if a time is set for the next execution, adjust the cronjob entry
                    if ($cronJob->getNextRun() instanceof \DateTime) {
                        $this->connection->executeQuery(
                            'UPDATE s_crontab SET next = ? WHERE action LIKE ? ',
                            [
                                $cronJob->getNextRun()->format('Y-m-d H:i:s'),
                                '%' . $cronJob->getAction()
                            ]
                        );
                    }
                    $log['success'] = true;
                } else {
                    $logs['success'] = false;
                }
            } else {
                $log['success'] = true;
                $log['message'] = 'Cronjob already exists';
            }

            $logs['cronjobs'][] = $log;
        }

        $this->loggingService->write(
            $plugin->getName(),
            __FUNCTION__,
            $logs['success'] ? 'Successful' : 'Error',
            ['cron' => $logs]
        );
    }

    /**
     * @param Plugin $plugin
     * @param string $name
     * @param string $action
     * @param int    $interval
     * @param int    $active
     *
     * @return int
     * @throws \Exception
     */
    protected function addCronJob(Plugin $plugin, $name, $action, $interval = 86400, $active = 1)
    {
        $pluginModel = $this->em->getRepository(PluginModel::class)
                                ->findOneBy(['name' => $plugin->getName()]);

        if (!$pluginModel instanceof PluginModel) {
            return 0;
        }

        return $this->connection->insert(
            's_crontab',
            [
                'name'       => $name,
                'action'     => $action,
                'next'       => new \DateTime(),
                'start'      => null,
                '`interval`' => $interval,
                'active'     => $active,
                'end'        => new \DateTime(),
                'pluginID'   => $pluginModel->getId(),
            ],
            [
                'next' => 'datetime',
                'end'  => 'datetime',
            ]
        );
    }

    /**
     * @param string $action
     * @return boolean
     */
    public function cronJobExists($action)
    {
        $sql = 'SELECT id FROM s_crontab WHERE s_crontab.action LIKE ?';
        $result = $this->connection->fetchColumn($sql, ['%' . $action]);

        return (bool) $result;
    }
}
