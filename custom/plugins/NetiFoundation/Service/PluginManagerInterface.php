<?php
/**
 * Copyright notice
 *
 * (c) 2009-2016 Net Inventors - Agentur für digitale Medien GmbH
 * All rights reserved
 *
 * This script is part of the Spirit-Project.
 * The Spirit-Project is property of the Net Inventors GmbH and
 * may not be used in projects not related to the Net Inventors
 * without explicit permission by the authors.
 *
 * PHP version 5
 *
 * @package    NetiFoundation
 * @subpackage NetiFoundation/PluginManagerInterface.php
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 * @deprecated
 */

namespace NetiFoundation\Service;

/**
 * Interface PluginManagerInterface
 *
 * @package    NetiFoundation\Service
 *
 * @deprecated 4.0.0 - typehint concrete class
 */
interface PluginManagerInterface
{
    public const EVENT_INSTALL = 'install';

    public const EVENT_UPDATE  = 'update';
}
