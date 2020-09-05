<?php

namespace NetiLanguageDetector\Component;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Class Setup
 *
 * @package NetiLanguageDetector\Component
 */
class Setup extends \Enlight_Class
{
    /**
     * @throws \Zend_Db_Statement_Exception
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function addTable()
    {

        $modelManager   = Shopware()->Models();
        $schemaTool     = new SchemaTool($modelManager);
        $tmpArr         = [];
        // Check if table s_neti_ip2location exists
        $query          = Shopware()->Db()->query("show tables like 's_neti_ip2location%'");
        if ($query->rowCount() == 0) {
            // If it does not exist, create it
            $schemaTool->createSchema(
                array(
                    $modelManager->getClassMetadata('NetiLanguageDetector\Models\IpLocation'),
                )
            );
        }

        // fill the table, if it is empty
        $query = Shopware()->Db()->query("select id from s_neti_ip2location");
        if ($query->rowCount() == 0) {
            $filePath = __DIR__ . '/../Data/IP2LOCATION-LITE-DB1.CSV' ;
            $csvdata = new \Shopware_Components_CsvIterator($filePath, ';');
            $k = 0;
            foreach ($csvdata as $newvalue) {
                $newvalue = array_values($newvalue);
                $tmpArr[$k++] = " (\"$newvalue[0]\"),";
            }
            $tmpArr = array_chunk($tmpArr,10000);
            foreach ($tmpArr as $item) {
                $sql = "INSERT INTO s_neti_ip2location (ip_from, ip_to, country_code, country_name) VALUES";
                $values = '';
                foreach ($item as $value) {
                    $values .= $value ;
                }
                $values = rtrim($values, ',');
                $sql .=  $values ;
                Shopware()->Db()->query($sql);
            }
           }

        // Check if table s_neti_currencylocation exists
        $query          = Shopware()->Db()->query("show tables like 's_neti_currencylocation%'");
        if ($query->rowCount() == 0) {
            // If it does not exist, create it
            $schemaTool->createSchema(
                array(
                    $modelManager->getClassMetadata('NetiLanguageDetector\Models\CurrencyLocation'),
                )
            );
        }

        // fill the table, if it is empty
        $query = Shopware()->Db()->query("select id from s_neti_currencylocation");

        if ($query->rowCount() == 0) {
            $filePath = __DIR__ . '/../Data/COUNTRY2CURRENCY.CSV' ;
            $csvdata = new \Shopware_Components_CsvIterator($filePath, ';');
            $k = 0;
            foreach ($csvdata as $newvalue) {
                $newvalue = array_values($newvalue);
                $tmpArr[$k++] = " (\"$newvalue[0]\"),";
            }
            $tmpArr = array_chunk($tmpArr,10000);
            foreach ($tmpArr as $item) {
                $sql = "INSERT INTO s_neti_currencylocation (country_name, country_code, currency_name, currency_code) VALUES";
                $values = '';
                foreach ($item as $value) {
                    $values .= $value ;
                }
                $values = rtrim($values, ',');
                $sql .=  $values ;
                Shopware()->Db()->query($sql);
            }
        }
    }
}
