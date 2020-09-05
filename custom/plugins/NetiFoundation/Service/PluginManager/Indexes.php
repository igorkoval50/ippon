<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\PluginManager;

use Doctrine\DBAL\Connection;
use NetiFoundation\Service\Logging\LoggingServiceInterface;
use NetiFoundation\Struct\PluginConfigFile\Index;
use Shopware\Components\Plugin;

/**
 * Class Indexes
 *
 * @package NetiFoundation\Service\PluginManager
 */
class Indexes implements IndexesInterface
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
     * @param Connection              $connection
     * @param LoggingServiceInterface $loggingService
     */
    public function __construct(
        Connection $connection,
        LoggingServiceInterface $loggingService
    ) {
        $this->connection     = $connection;
        $this->loggingService = $loggingService;
    }

    /**
     * @param Plugin  $plugin
     * @param Index[] $values
     */
    public function removeIndexes(Plugin $plugin, array $values)
    {
        // Todo: Implement logging... therefore the param $plugin is required

        foreach ($values as $index) {
            $table   = $index->getTable();
            $columns = $index->getColumns();

            foreach ($columns as $name => $column) {
                try {
                    /** @noinspection SqlResolve */
                    $this->connection->query(
                        'DROP INDEX `' . $name . '` ON ' . $table . ';'
                    );
                } catch (\Exception $e) {
                }
            }
        }
    }

    /**
     * @param Plugin  $plugin
     * @param Index[] $values
     */
    public function createIndexes(Plugin $plugin, array $values)
    {
        $this->updateIndexes($plugin, $values);
    }

    /**
     * @param Plugin  $plugin
     * @param Index[] $values
     */
    public function updateIndexes(Plugin $plugin, array $values)
    {
        $logs = ['success' => true, 'indexes' => []];

        foreach ($values as $index) {
            $table   = $index->getTable();
            $type    = $index->getType();
            $columns = $index->getColumns();

            foreach ($columns as $name => $col) {
                $log = [
                    'table'     => $table,
                    'type'      => $type,
                    'column(s)' => $col,
                    'success'   => false,
                ];

                $typeSql = <<<SQL
DESCRIBE {$table} {$col}
SQL;

                $modifier = '';
                if ('unique' !== $type) {
                    $description = $this->connection->fetchAssoc($typeSql);

                    $columnType = $description['Type'];
                    $columnType = \substr($columnType, 0, \strpos($columnType, '(') ?: null);

                    if (\in_array(\strtolower($columnType), ['text', 'char', 'varchar'])) {
                        $modifier = 'FULLTEXT ';
                    }
                } elseif ('unique' === $type) {
                    $modifier = 'UNIQUE ';
                }

                try {
                    $sql = <<<SQL
CREATE {$modifier}INDEX `{$name}` ON {$table} ({$col});
SQL;
                    $this->connection->query($sql);
                    $log['success'] = true;
                } catch (\Exception $e) {
                    $logs['success'] = false;
                    $logs['message'] = 'Error ' . $e->getCode() . ': ' . $e->getMessage();
                }
                $logs['indexes'][] = $log;
            }
        }

        $this->loggingService->write(
            $plugin->getName(),
            __FUNCTION__,
            $logs['success'] ? 'Successful' : 'Error',
            ['indexes' => $logs]
        );
    }
}

