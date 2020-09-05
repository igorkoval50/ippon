<?php
/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   NetiLanguageDetector
 * @author     bmueller
 */

namespace NetiLanguageDetector\Service;

use NetiFoundation\Service\PluginManager\Config;
use NetiLanguageDetector\Struct\PluginConfig;

/**
 * Class Location
 *
 * @package NetiLanguageDetector\Service
 */
class Location implements LocationInterface
{
    /**
     * @var Config
     */
    private $configService;

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $connection;

    /**
     * @var Debug
     */
    private $debug;

    public function __construct(
        Config $configService,
        \Enlight_Components_Db_Adapter_Pdo_Mysql $connection,
        Debug $debug
    ) {
        $this->configService = $configService;
        $this->connection    = $connection;
        $this->debug         = $debug;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getUsersLocation(\Enlight_Controller_Request_RequestHttp $request)
    {
        $detectionMethod = $this->getConfig()->get('detectionMethod', 'both');
        $struserdet      = [];

        if ('browser' === $detectionMethod || 'both' === $detectionMethod) {
            // check by browser language, if possible
            if (false !== $request->getHeader('ACCEPT_LANGUAGE')) {
                $locale = $this->getAcceptLanguageFromHttp(
                    $request->getHeader('ACCEPT_LANGUAGE')
                );

                if (preg_match('/[a-z]{2,3}([_-][A-Z][a-z]{3})?[_-][A-Z]{2}/', $locale)) {
                    $country = substr($locale, -2);
                } else {
                    // We only have the language part of the locale
                    $country = $this->resolveCountryFromLanguage($locale);
                }

                $this->debug->log(sprintf(
                    'Browser reported locale "%s", indicates country "%s"',
                    $locale,
                    $country
                ));

                if (false !== $country) {
                    $sql = <<<'SQL'
SELECT DISTINCT
    ( `country_code` ),
    `country_name`
    FROM
        `s_neti_ip2location`
    WHERE
        `country_code` = ?
SQL;

                    $result     = $this->connection->fetchRow($sql, array($country));

                    if ($result) {
                        $struserdet = array_values($result);

                        $this->debug->log(\sprintf(
                            'Database query for country code "%s" successful.',
                            $country
                        ), ['result' => $result]);
                    } else {
                        $this->debug->log(\sprintf(
                            'Database query for country code "%s" returned no result.',
                            $country
                        ));
                    }
                }
            }
        }

        if (empty($struserdet) && ('ip' === $detectionMethod || 'both' === $detectionMethod)) {
            // #22944 also check a potential proxy
            $clientIps = explode(',', $this->getRealIpAddr());
            $clientIp  = $clientIps[0];

            $this->debug->log(\sprintf('Remote IP: "%s"', $clientIp));

            /** Test cases */
            //$clientIp = '7.255.255.255'; // USA
            //$clientIp = '159.20.253.221'; // Italia
            //$clientIp = '31.18.64.61'; // Germany
            //$clientIp = '128.199.118.208'; // Singapore
            //$clientIp = '143.252.148.217'; // Great Britain
            //$clientIp = '149.202.94.120'; // France
            //$clientIp = '194.6.172.98'; // Switzerland
            //$clientIp = '213.55.176.202'; // Switzerland
            //$clientIp = '109.169.63.47'; // Japan

            $ips      = explode('.', $clientIp);
            $netblock = ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);

            /** getting Country code of the user from the table using the user ipaddress */
            $sql    = <<<'SQL'
SELECT DISTINCT(`country_code`),`country_name` FROM `s_neti_ip2location` WHERE ? BETWEEN `ip_from` AND `ip_to` 
SQL;
            $result = $this->connection->fetchRow($sql, array($netblock));

            if ($result) {
                $struserdet = array_values($result);

                $this->debug->log(\sprintf(
                    'Database query for ip block "%s" successful.',
                    $netblock
                ), ['result' => $result]);
            } else {
                $this->debug->log(\sprintf(
                    'Database query for ip block "%s" returned no result.',
                    $netblock
                ));
            }
        }

        $this->debug->log(\sprintf('Method %s returned: ', __METHOD__), ['return' => $struserdet]);

        return $struserdet;
    }

    /**
     * @return mixed
     */
    private static function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * @return PluginConfig
     */
    public function getConfig()
    {
        /**
         * @var PluginConfig $config
         */
        $config = $this->configService->getPluginConfig($this);

        return $config;
    }

    /**
     * @return bool
     */
    protected function intlAvailable()
    {
        return extension_loaded('intl');
    }

    /**
     * @param $httpAcceptLanguage
     *
     * @return string
     */
    protected function getAcceptLanguageFromHttp($httpAcceptLanguage)
    {
        if ($this->intlAvailable()) {
            return \Locale::acceptFromHttp($httpAcceptLanguage);
        }

        return substr($httpAcceptLanguage, 0, strpos($httpAcceptLanguage, ','));
    }

    /**
     * @param $language
     *
     * @return bool|string
     */
    protected function resolveCountryFromLanguage($language)
    {
        if (!$this->intlAvailable()) {
            return strtoupper($language);
        }

        $len            = strlen($language);
        $matches        = [];
        $preferredValue = false;

        foreach (\ResourceBundle::getLocales('') as $possibleLocale) {
            if (strlen($possibleLocale) <= $len) {
                // The matching locale string length cannot be
                // smaller or equal the language part of the locale
                continue;
            }

            if (substr($possibleLocale, 0, $len) === $language) {
                $countryPart              = substr($possibleLocale, -2);
                $matches[$possibleLocale] = $countryPart;

                if (strtoupper($language) === $countryPart) {
                    $preferredValue = $countryPart;
                    break;
                }
            }
        }

        if (false !== $preferredValue) {
            return $preferredValue;
        }

        reset($matches);

        return current($matches);
    }
}
