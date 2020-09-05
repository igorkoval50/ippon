<?php

namespace NetiLanguageDetector\Updates;

use NetiFoundation\Updates\AbstractUpdate;

class Update_5_1_0 extends AbstractUpdate
{
    /**
     * Run the update
     *
     * @return bool
     * @throws \Exception
     */
    protected function run()
    {
        $tmpArr = [];

        try {
            $filePath = __DIR__ . '/../Data/IP2LOCATION-LITE-DB1.CSV';
            $csvdata  = new \Shopware_Components_CsvIterator($filePath, ';');
            $k        = 0;
            $this->db->query('TRUNCATE s_neti_ip2location');

            foreach ($csvdata as $newvalue) {
                $newvalue     = array_values($newvalue);
                $tmpArr[$k++] = " (\"$newvalue[0]\"),";
            }
            $tmpArr = array_chunk($tmpArr, 10000);
            foreach ($tmpArr as $item) {
                $sql    = "INSERT INTO s_neti_ip2location (ip_from, ip_to, country_code, country_name) VALUES";
                $values = '';
                foreach ($item as $value) {
                    $values .= $value;
                }
                $values = rtrim($values, ',');
                $sql    .= $values;
                $this->db->query($sql);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }
}