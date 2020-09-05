<?php
namespace MagNewsletterBox\Bootstrap;

use Doctrine\DBAL\Connection;

class DatabaseHandler
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Install all promotion tables
     */
    public function installTables()
    {
        $this->createNewsletterBoxTable();
    }

    /**
     * creates 's_plugin_mag_emarketing_voucher_codes'
     */
    public function createNewsletterBoxTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `s_plugin_mag_emarketing_voucher_codes` (
								`id` int(11) NOT NULL AUTO_INCREMENT,
								`voucherID` int(11) NOT NULL,
								`voucherCodeID` int(11) NOT NULL,
								`email` text COLLATE utf8_unicode_ci NOT NULL, 
								PRIMARY KEY (`id`)
								)
								ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;';

        $this->connection->exec($sql);
    }
}