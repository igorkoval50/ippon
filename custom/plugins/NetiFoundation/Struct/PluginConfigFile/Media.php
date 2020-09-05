<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct\PluginConfigFile;

use NetiFoundation\Struct\AbstractClass;
use NetiFoundation\Struct\PluginConfigFile\Media\Album;

/**
 * Class Media
 *
 * @package NetiFoundation\Struct\PluginConfigFile
 */
class Media extends AbstractClass
{
    /**
     * @var array
     */
    protected $albums;

    /**
     * Gets the value of albums from the record
     *
     * @return Album[]
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * Sets the Value to albums in the record
     *
     * @param array $albums
     *
     * @return self
     */
    public function setAlbums($albums)
    {
        if ($albums) {
            if ($this->isAssoc($albums)) {
                $albums = [
                    new Album($albums)
                ];
            } else {
                $albumsStructs = [];
                foreach ($albums as $entry) {
                    $albumsStructs[] = new Album($entry);
                }
                $albums = $albumsStructs;
            }

            $this->albums = $albums;
        } else {
            $this->albums = null;
        }

        return $this;
    }
}
