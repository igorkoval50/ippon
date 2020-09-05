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

namespace SwagFuzzy\Bootstrap;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOStatement;
use SwagFuzzy\Components\ExactMatchAlgorithm;
use SwagFuzzy\Components\KeywordAlgorithms;

/**
 * Class UpdateMergeAlgorithmSettings
 *
 * Handles field merge in Settings
 */
class UpdateMergeAlgorithmSettings
{
    /**
     * Set the default database name for this plugin
     */
    const TABLE_NAME = 's_plugin_swag_fuzzy_settings';

    /**
     * @var
     */
    private $preUpdateData = [];

    /**
     * Rewrite data map
     *
     * @var array
     */
    private $map = [
        'keyword_algorithm' => [
            'useSoundex' => KeywordAlgorithms::TYPE_SOUNDEX,
            'useColognePhonetic' => KeywordAlgorithms::TYPE_COLOGNE_PHONETIC,
            'useMetaphone' => KeywordAlgorithms::TYPE_METAPHONE,
        ],
        'exact_match_algorithm' => [
            'useLevenshtein' => ExactMatchAlgorithm::TYPE_LEVINSTEIN,
            'useSimilarText' => ExactMatchAlgorithm::TYPE_SIMILAR_TEXT,
        ],
    ];

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function update()
    {
        $this->connection->beginTransaction();

        try {
            $this->storeOldRowData();

            $this->migrateTable();

            $this->writeNewRowData();
        } catch (\Exception $e) {
            $this->connection->rollBack();

            return false;
        }

        $this->connection->commit();

        return true;
    }

    private function migrateTable()
    {
        $diffSql = 'ALTER TABLE s_plugin_swag_fuzzy_settings
                    ADD keyword_algorithm VARCHAR(255) NOT NULL,
                    ADD exact_match_algorithm VARCHAR(255) NOT NULL,
                    DROP useSoundex,
                    DROP useColognePhonetic,
                    DROP useMetaphone,
                    DROP useLevenshtein,
                    DROP useSimilarText';

        $this->connection->exec($diffSql);
    }

    /**
     * Call on preUpdate
     */
    private function storeOldRowData()
    {
        $qb = $this->getSelectQueryBuilder();

        /** @var PDOStatement $stmt */
        $stmt = $qb->execute();

        $this->preUpdateData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * call on post update
     */
    private function writeNewRowData()
    {
        foreach ($this->preUpdateData as $row) {
            $updates = [];

            foreach ($this->map as $newFieldName => $formerFieldNames) {
                $toWrite = null;

                $toWrite = $this->findValueToWrite($formerFieldNames, $row);

                if (!$toWrite) {
                    break;
                }

                $updates[$newFieldName] = $toWrite;
            }

            $this->updateSettingsField($row['id'], $updates);
        }
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getSelectQueryBuilder()
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'id',
                'useSoundex',
                'useColognePhonetic',
                'useMetaphone',
                'useLevenshtein',
                'useSimilarText'
            )
            ->from(self::TABLE_NAME, 'settings');
    }

    /**
     * @param $id
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    private function updateSettingsField($id, array $updates)
    {
        if (!$id) {
            throw new \InvalidArgumentException('Unable to continue, id is missing');
        }

        if (!count($updates)) {
            return;
        }

        $qb = $this->connection->createQueryBuilder()
            ->update(self::TABLE_NAME, 'settings')
            ->where('settings.id = :id');

        foreach ($updates as $fieldName => $value) {
            $qb->set('settings.' . $fieldName, ':' . $fieldName);
            $qb->setParameter(':' . $fieldName, $value);
        }

        $qb->setParameter(':id', $id);

        $affectedRows = $qb->execute();

        if ($affectedRows !== 1) {
            throw new \RuntimeException('Number of affected rows did not match expectations (1 != "' . $affectedRows . '"), rollback!');
        }
    }

    /**
     * @return string
     */
    private function findValueToWrite(array $formerFieldNames, array $row)
    {
        foreach ($formerFieldNames as $formerFieldName => $newValue) {
            if ($row[$formerFieldName] != 1) {
                continue;
            }

            return $newValue;
        }

        return current(
            reset($formerFieldNames)
        );
    }
}
