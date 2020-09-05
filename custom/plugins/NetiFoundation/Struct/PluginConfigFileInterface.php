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
 * @subpackage NetiFoundation/PluginConfigFileInterface.php
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
namespace NetiFoundation\Struct;

use NetiFoundation\Struct\PluginConfigFile\Acl;
use NetiFoundation\Struct\PluginConfigFile\Attribute;
use NetiFoundation\Struct\PluginConfigFile\CronJob;
use NetiFoundation\Struct\PluginConfigFile\Formfield;
use NetiFoundation\Struct\PluginConfigFile\Index;
use NetiFoundation\Struct\PluginConfigFile\Media;
use NetiFoundation\Struct\PluginConfigFile\MenuEntry;

/**
 * Interface PluginConfigFileInterface
 *
 * @package NetiFoundation\Struct
 */
interface PluginConfigFileInterface
{
    /**
     * Gets the value of models from the record
     *
     * @return array
     */
    public function getModels();

    /**
     * Sets the Value to models in the record
     *
     * @param array $models
     *
     * @return \NetiFoundation\Struct\PluginConfigFile
     */
    public function setModels($models);

    /**
     * Gets the value of attributes from the record
     *
     * @return Attribute[]
     */
    public function getAttributes();

    /**
     * Sets the Value to attributes in the record
     *
     * @param array $attributes
     *
     * @return \NetiFoundation\Struct\PluginConfigFile
     */
    public function setAttributes($attributes);

    /**
     * Gets the value of indexes from the record
     *
     * @return Index[]
     */
    public function getIndexes();

    /**
     * Sets the Value to indexes in the record
     *
     * @param array $indexes
     *
     * @return \NetiFoundation\Struct\PluginConfigFile
     */
    public function setIndexes($indexes);

    /**
     * Gets the value of form from the record
     *
     * @return Formfield[]
     */
    public function getForm();

    /**
     * Sets the Value to form in the record
     *
     * @param array $formfields
     *
     * @return \NetiFoundation\Struct\PluginConfigFile
     */
    public function setForm($formfields);

    /**
     * Gets the value of acl from the record
     *
     * @return Acl[]
     */
    public function getAcl();

    /**
     * Sets the Value to acl in the record
     *
     * @param array $acl
     *
     * @return \NetiFoundation\Struct\PluginConfigFile
     */
    public function setAcl($acl);

    /**
     * Gets the value of media from the record
     *
     * @return Media
     */
    public function getMedia();

    /**
     * Sets the Value to media in the record
     *
     * @param array $media
     *
     * @return \NetiFoundation\Struct\PluginConfigFile
     */
    public function setMedia($media);

    /**
     * Gets the value of cronJobs from the record
     *
     * @return CronJob[]
     */
    public function getCronJobs();

    /**
     * Sets the Value to cronJobs in the record
     *
     * @param array $cronJobs
     *
     * @return \NetiFoundation\Struct\PluginConfigFile
     */
    public function setCronJobs($cronJobs);

    /**
     * Gets the value of menu from the record
     *
     * @return MenuEntry[]
     */
    public function getMenu();

    /**
     * Sets the Value to menu in the record
     *
     * @param array $menu
     *
     * @return \NetiFoundation\Struct\PluginConfigFile
     */
    public function setMenu($menu);
}
