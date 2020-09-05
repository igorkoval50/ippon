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

namespace SwagBusinessEssentials\Setup\Migration;

use Doctrine\DBAL\Connection;
use PDO;

abstract class Table implements TableInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $backupTableName;

    /**
     * @var array
     */
    protected $backupTableSchema;

    /**
     * @var int
     */
    protected $backupDataTotalCount;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var string
     */
    protected $createTableSql;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function rename()
    {
        $this->renameTable($this->getTableName(), $this->getBackupTableName());
    }

    public function create()
    {
        $this->createTable($this->getCreateQuery());
    }

    public function migrate()
    {
        while ($this->getOffset() < $this->getBackupDataTotalCount()) {
            $data = $this->getData();

            if (count($data) === 0) {
                return;
            }

            $sql = $this->getInsertSql($data);
            $this->connection->prepare($sql)->execute();
        }
    }

    public function revert()
    {
        if ($this->tableExists($this->getBackupTableName())) {
            $this->dropTable($this->getTableName());
        }

        $this->renameTable($this->getBackupTableName(), $this->getTableName());
    }

    /**
     * return the backupTableName.
     *
     * @return string
     */
    public function getBackupTableName()
    {
        if ($this->backupTableName === null) {
            $this->backupTableName = $this->createBackupTableName($this->getTableName());
        }

        return $this->backupTableName;
    }

    /**
     * return the table schema as array.
     *
     * @return array
     */
    public function getBackupTableSchema()
    {
        if ($this->backupTableSchema === null) {
            $this->backupTableSchema = $this->getTableSchema($this->getBackupTableName());
        }

        return $this->backupTableSchema;
    }

    /**
     * return the count of all data in backupTable.
     *
     * @return int
     */
    public function getBackupDataTotalCount()
    {
        if ($this->backupDataTotalCount === null) {
            $this->backupDataTotalCount = $this->getTotalCount($this->getBackupTableName());
        }

        return $this->backupDataTotalCount;
    }

    /**
     * return the current data offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * sets the new offset.
     *
     * @param int $value
     */
    public function setOffset($value)
    {
        $this->offset = $value;
    }

    /**
     * return the create table mysql string.
     *
     * @return string
     */
    public function getCreateQuery()
    {
        if ($this->createTableSql === null) {
            $this->createTableSql = $this->generateCreateTableSql($this->getTableName(), $this->getBackupTableSchema());
        }

        return $this->createTableSql;
    }

    /**
     * Drops the database table.
     *
     * @return bool
     */
    public function drop()
    {
        $sql = 'DROP TABLE IF EXISTS ' . $this->getTableName();
        $this->connection->exec($sql);

        return true;
    }

    /**
     * @return array
     */
    protected function getData()
    {
        $sql = $this->getSelectSql();

        $data = $this->connection->executeQuery($sql)->fetchAll(PDO::FETCH_ASSOC);

        $this->setOffset($this->getOffset() + count($data));

        return $data;
    }

    /**
     * @return string
     */
    protected function getSelectSql()
    {
        return implode(' ', [
            'SELECT * FROM',
            '', $this->getBackupTableName(),
            'LIMIT 20',
            'OFFSET',
            $this->getOffset(),
        ]);
    }

    /**
     * @return string
     */
    protected function getInsertSql(array $data)
    {
        return implode(' ', [
            'INSERT INTO',
            $this->getTableName(),
            $this->createInsertColumns($data),
            'VALUES',
            $this->getValues($data),
        ]);
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    protected function createBackupTableName($tableName)
    {
        $suffix = '_' . time() . '_backup';

        return $tableName . $suffix;
    }

    /**
     * @param string $tableName
     *
     * @return array
     */
    protected function getTableSchema($tableName)
    {
        $sql = 'SHOW COLUMNS FROM ' . $tableName;

        return $this->connection->executeQuery($sql)->fetchAll();
    }

    /**
     * @param string $tableName
     *
     * @return bool|string
     */
    protected function getTotalCount($tableName)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $tableName;

        return $this->connection->executeQuery($sql)->fetchColumn();
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    protected function generateCreateTableSql($tableName, array $config)
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $tableName . ' (
             id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,';

        $maxCount = count($config) - 1;
        for ($i = 0; $i <= $maxCount; ++$i) {
            if ($config[$i]['Field'] === 'id') {
                continue;
            }

            $sql .= implode(' ', [
                $config[$i]['Field'],
                $config[$i]['Type'],
                $config[$i]['Null'] === 'NO' ? 'NOT NULL' : '',
                $i !== $maxCount ? ',' : ')',
            ]);
        }

        return $sql;
    }

    /**
     * @param string $fromTableName
     * @param string $toTableName
     */
    protected function renameTable($fromTableName, $toTableName)
    {
        $sql = 'RENAME TABLE ' . $fromTableName . ' TO ' . $toTableName;
        $this->connection->prepare($sql)->execute();
    }

    /**
     * @param string $newTableSchemaSql
     */
    protected function createTable($newTableSchemaSql)
    {
        $this->connection->executeQuery($newTableSchemaSql);
    }

    /**
     * @param array[] $data
     *
     * @return string
     */
    protected function createInsertColumns(array $data)
    {
        $columns = [];
        foreach ($data[0] as $columnName => $value) {
            if ($columnName === 'id') {
                continue;
            }

            $columns[] = $columnName;
        }

        return '(' . implode(',', $columns) . ')';
    }

    /**
     * @param array[] $data
     *
     * @return string
     */
    protected function getValues(array $data)
    {
        $returnValue = [];

        foreach ($data as $row) {
            $values = [];
            foreach ($row as $columnName => $value) {
                if ($columnName === 'id') {
                    continue;
                }
                $values[] = "'" . $value . "'";
            }

            $returnValue[] = '(' . implode(',', $values) . ')';
        }

        return implode(',', $returnValue);
    }

    /**
     * @param string $tableName
     */
    protected function dropTable($tableName)
    {
        $sql = 'DROP TABLE IF EXISTS ' . $tableName;

        $this->connection->executeQuery($sql);
    }

    /**
     * @param string $tableName
     *
     * @return array|bool
     */
    protected function tableExists($tableName)
    {
        $sql = "SHOW TABLES LIKE '" . $tableName . "'";

        return $this->connection->executeQuery($sql)->fetch();
    }
}
