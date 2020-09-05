<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct;

use NetiFoundation\Struct\PluginConfigFile\{Acl, Attribute, CronJob, Formfield, Index, Media, MenuEntry};

/**
 * Class PluginConfigFile
 *
 * @package NetiFoundation\Struct
 */
class PluginConfigFile extends AbstractClass
{
    /**
     * @var array
     */
    protected $models;

    /**
     * @var Attribute[]
     */
    protected $attributes;

    /**
     * @var Index[]
     */
    protected $indexes;

    /**
     * @var Formfield[]
     */
    protected $formfields;

    /**
     * @var Acl[]
     */
    protected $acl;

    /**
     * @var Media
     */
    protected $media;

    /**
     * @var CronJob[]
     */
    protected $cronJobs;

    /**
     * @var MenuEntry[]
     */
    protected $menuEntries;

    /**
     * Gets the value of models from the record
     *
     * @return array
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * Sets the Value to models in the record
     *
     * @param array $models
     *
     * @return self
     */
    public function setModels($models)
    {
        if ($models) {
            $this->models = $models;
        } else {
            $this->models = null;
        }

        return $this;
    }

    /**
     * Gets the value of attributes from the record
     *
     * @return Attribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets the Value to attributes in the record
     *
     * @param array $attributes
     *
     * @return self
     */
    public function setAttributes($attributes)
    {
        if ($attributes) {
            if ($this->isAssoc($attributes)) {
                $attributes = [
                    new Attribute($attributes)
                ];
            } else {
                $attributesStructs = [];
                foreach ($attributes as $entry) {
                    $attributesStructs[] = new Attribute($entry);
                }
                $attributes = $attributesStructs;
            }

            $this->attributes = $attributes;
        } else {
            $this->attributes = null;
        }

        return $this;
    }

    /**
     * Gets the value of indexes from the record
     *
     * @return Index[]
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Sets the Value to indexes in the record
     *
     * @param array $indexes
     *
     * @return self
     */
    public function setIndexes($indexes)
    {
        if ($indexes) {
            $indexesStructs = [];
            if ($this->isAssoc($indexes)) {
                foreach ($indexes as $key => $entry) {
                    foreach ($entry as $item) {
                        if (! isset($item['table'])) {
                            $item['table'] = $key;
                        }
                        $indexesStructs[] = new Index($item);
                    }
                }
            } else {
                foreach ($indexes as $entry) {
                    $indexesStructs[] = new Index($entry);
                }
            }

            $this->indexes = $indexesStructs;
        } else {
            $this->indexes = null;
        }

        return $this;
    }

    /**
     * Gets the value of form from the record
     *
     * @return Formfield[]
     */
    public function getForm()
    {
        return $this->formfields;
    }

    /**
     * Sets the Value to form in the record
     *
     * @param array $formfields
     *
     * @return self
     */
    public function setForm($formfields)
    {
        if ($formfields) {
            if ($this->isAssoc($formfields)) {
                $formfields = [
                    new Formfield($formfields)
                ];
            } else {
                $formfieldStructs = [];
                foreach ($formfields as $entry) {
                    $formfieldStructs[] = new Formfield($entry);
                }
                $formfields = $formfieldStructs;
            }

            $this->formfields = $formfields;
        } else {
            $this->formfields = null;
        }

        return $this;
    }

    /**
     * Gets the value of acl from the record
     *
     * @return Acl[]
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * Sets the Value to acl in the record
     *
     * @param array $acl
     *
     * @return self
     */
    public function setAcl($acl)
    {
        if ($acl) {
            $aclStructs = [];
            if ($this->isAssoc($acl)) {
                foreach ($acl as $key => $entry) {
                    $aclStructs[] = new Acl([
                        'resourceName' => $key,
                        'privileges'   => $entry
                    ]);
                }
            } else {
                foreach ($acl as $entry) {
                    $aclStructs[] = new Acl($entry);
                }
            }

            $this->acl = $aclStructs;
        } else {
            $this->acl = null;
        }

        return $this;
    }

    /**
     * Gets the value of media from the record
     *
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Sets the Value to media in the record
     *
     * @param array $media
     *
     * @return self
     */
    public function setMedia($media)
    {
        if ($media) {
            $this->media = new Media($media);
        } else {
            $this->media = null;
        }

        return $this;
    }

    /**
     * Gets the value of cronJobs from the record
     *
     * @return CronJob[]
     */
    public function getCronJobs()
    {
        return $this->cronJobs;
    }

    /**
     * Sets the Value to cronJobs in the record
     *
     * @param array $cronJobs
     *
     * @return self
     */
    public function setCronJobs($cronJobs)
    {
        if ($cronJobs) {
            $cronJobsStructs = [];
            foreach ($cronJobs as $entry) {
                $cronJobsStructs[] = new CronJob($entry);
            }

            $this->cronJobs = $cronJobsStructs;
        } else {
            $this->cronJobs = null;
        }

        return $this;
    }

    /**
     * Gets the value of menu from the record
     *
     * @return MenuEntry[]
     */
    public function getMenu()
    {
        return $this->menuEntries;
    }

    /**
     * Sets the Value to menu in the record
     *
     * @param array $menu
     *
     * @return self
     */
    public function setMenu($menu)
    {
        if ($menu) {
            if ($this->isAssoc($menu)) {
                $menu = [
                    new MenuEntry($menu)
                ];
            } else {
                $menuStructs = [];
                foreach ($menu as $entry) {
                    $menuStructs[] = new MenuEntry($entry);
                }
                $menu = $menuStructs;
            }

            $this->menuEntries = $menu;
        } else {
            $this->menuEntries = null;
        }

        return $this;
    }
}
