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

namespace SwagFuzzy\Bundle\SearchBundleDBAL\SearchTerm;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\SearchBundleDBAL\SearchTerm\SearchIndexerInterface;
use SwagFuzzy\Components\ColognePhonetic;
use SwagFuzzy\Components\Metaphone;

/**
 * Class FuzzySearchIndexer
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class FuzzySearchIndexer implements SearchIndexerInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ColognePhonetic
     */
    private $colognePhonetic;

    /**
     * @var SearchIndexerInterface
     */
    private $coreSearchIndexer;

    /**
     * @var Metaphone
     */
    private $metaphone;

    /**
     * initialises the private properties
     */
    public function __construct(
        Connection $connection,
        ColognePhonetic $colognePhonetic,
        Metaphone $metaphone,
        SearchIndexerInterface $searchIndexer
    ) {
        $this->connection = $connection;
        $this->colognePhonetic = $colognePhonetic;
        $this->metaphone = $metaphone;
        $this->coreSearchIndexer = $searchIndexer;
    }

    /**
     * decorates the build method of the original Indexer to add the
     * soundex, cologne phonetic and metaphone values of each keyword to the DB
     *
     * {@inheritdoc}
     */
    public function build()
    {
        $this->coreSearchIndexer->build();

        // sets the soundex of each keywords via SQL
        $this->connection->exec(
            "UPDATE `s_search_keywords` SET `soundex` = IF(SOUNDEX(`keyword`)='', NULL, SOUNDEX(`keyword`))"
        );

        $builder = $this->connection->createQueryBuilder();

        $builder->select('ssk.id', 'ssk.keyword', 'ssk.cologne_phonetic', 'ssk.metaphone')
                ->from('s_search_keywords', 'ssk')
                ->where('ssk.cologne_phonetic IS NULL')
                ->orWhere('ssk.metaphone IS NULL');

        /** @var \PDOStatement $builderExecute */
        $builderExecute = $builder->execute();
        $keywordsWithoutCologneOrMetaphone = $builderExecute->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($keywordsWithoutCologneOrMetaphone)) {
            $this->updateKeywordTable($keywordsWithoutCologneOrMetaphone);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->coreSearchIndexer->validate();
    }

    /**
     * helper method to set cologne phonetic and metaphone
     *
     * @param $keywordsWithoutCologneOrMetaphone
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function updateKeywordTable($keywordsWithoutCologneOrMetaphone)
    {
        $sql = '';

        foreach ($keywordsWithoutCologneOrMetaphone as $key => $keyword) {
            $colognePhonetic = null;
            $metaphone = null;
            $id = $keyword['id'];

            if (is_null($keyword['cologne_phonetic'])) {
                $colognePhonetic = $this->colognePhonetic->getPhoneticHash($keyword['keyword']);
                $colognePhonetic = $this->connection->quote($colognePhonetic);
            }

            if (is_null($keyword['metaphone'])) {
                $metaphone = $this->metaphone->getPhoneticHash($keyword['keyword']);
                $metaphone = $this->connection->quote($metaphone);
            }

            if (isset($colognePhonetic) && isset($metaphone)) {
                $set = "SET `cologne_phonetic` = $colognePhonetic, `metaphone` = $metaphone";
            } elseif (isset($colognePhonetic)) {
                $set = "SET `cologne_phonetic` = $colognePhonetic";
            } elseif (isset($metaphone)) {
                $set = "SET `metaphone` = $metaphone";
            }

            $sql .= "UPDATE `s_search_keywords`
                     $set
                     WHERE `id` = $id;";

            if ($key % 100 === 0 && $key !== 0) {
                $this->connection->exec($sql);
                $sql = '';
            }
        }

        $this->connection->exec($sql);
    }
}
