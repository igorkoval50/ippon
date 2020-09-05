<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components;

class Installer extends InstallManager
{
    /**
     * Runs the installer.
     */
    public function run()
    {
        $success = [];

        $success[] = $this->createConfigForm();

        return $this->isSuccess($success);
    }
}
