<?php
/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   NetiLanguageDetector
 * @author     bmueller
 */

namespace NetiLanguageDetector\Service;

use NetiLanguageDetector\Struct\PluginConfig;

/**
 * Interface LocationInterface
 *
 * @package NetiLanguageDetector\Service
 */
interface LocationInterface
{
    /**
     * @param \Enlight_Controller_Request_RequestHttp $request
     *
     * @return array
     */
    public function getUsersLocation(\Enlight_Controller_Request_RequestHttp $request);

    /**
     * @return PluginConfig
     */
    public function getConfig();
}
