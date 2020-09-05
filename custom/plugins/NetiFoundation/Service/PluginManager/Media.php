<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\PluginManager;

use NetiFoundation\Service\Logging\LoggingServiceInterface;
use NetiFoundation\Struct\PluginConfigFile\Media\Album;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Models\Media\Album as SwAlbum;
use Shopware\Models\Media\Settings;

/**
 * Class Media
 *
 * @package NetiFoundation\Service\PluginManager
 */
class Media implements MediaInterface
{
    /**
     * @var ModelManager
     */
    protected $em;

    /**
     * @var LoggingServiceInterface
     */
    protected $loggingService;

    /**
     * @param ModelManager            $em
     * @param LoggingServiceInterface $loggingService
     */
    public function __construct(
        ModelManager $em,
        LoggingServiceInterface $loggingService
    ) {
        $this->em             = $em;
        $this->loggingService = $loggingService;
    }

    /**
     * @param Plugin   $plugin
     * @param Album[] $albums
     */
    public function addAlbums(Plugin $plugin, array $albums)
    {
        $this->updateAlbums($plugin, $albums);
    }

    /**
     * @param Plugin   $plugin
     * @param Album[] $albums
     */
    public function updateAlbums(Plugin $plugin, array $albums)
    {
        $logs        = ['success' => true, 'albums' => []];
        $needToFlush = false;
        $repository  = $this->em->getRepository('Shopware\Models\Media\Album');

        try {
            foreach ($albums as $album) {
                $albumSettings = $album->getSettings();
                $albumIcon = $album->getIcon();
                $albumName = $album->getName();
                $albumModel = $repository->findOneBy(array(
                    'name' => $albumName
                ));

                $log = array(
                    'name' => $albumName,
                    'success' => false
                );

                if (!$albumModel instanceof SwAlbum) {
                    $albumModel = new SwAlbum();
                }

                $settings = $albumModel->getSettings();
                if (!$settings instanceof Settings) {
                    $settings = new Settings();
                    $settings->setAlbum($albumModel);
                }

                // a null value in the settings leads to a fatal error
                $albumArray = $album->toArray();
                if (null === $albumArray['settings']) {
                    unset($albumArray['settings']);
                }

                // a null value in the position leads to an integrity constraint violation (not nullable)
                if (null === $albumArray['position']) {
                    $albumArray['position'] = 0;
                }

                $albumArray['settings'] = $settings;
                $albumModel->fromArray($albumArray);

                if (!empty($albumSettings)) {
                    $settings->fromArray($albumSettings->toArray());
                } else {
                    $settings->setCreateThumbnails(0);
                    $settings->setIcon($albumIcon);
                    $settings->setThumbnailSize('');
                }

                $albumModel->setSettings($settings);

                $this->em->persist($albumModel);

                $log['success']   = true;
                $logs['albums'][] = $log;
            }

            if ($needToFlush) {
                $this->em->flush();
            }
        } catch (\Exception $e) {
            $logs['success'] = false;
            $logs['message'] = 'Error ' .  $e->getCode() . ': ' . $e->getMessage();
        }

        $this->loggingService->write(
            $plugin->getName(),
            __FUNCTION__,
            $logs['success'] ? 'Successful' : 'Error',
            ['media' => $logs]
        );
    }

    /**
     * @param Plugin $plugin
     * @param Album[] $albums
     */
    public function removeAlbums(Plugin $plugin, array $albums)
    {
        // Todo: Implement logging... therefore the param $plugin is required
        
        $repository = $this->em->getRepository('Shopware\Models\Media\Album');
        foreach ($albums as $album) {
            $albumName  = $album->getName();
            $albumModel = $repository->findOneBy(array(
                'name' => $albumName
            ));

            if ($albumModel instanceof SwAlbum) {
                $albumSettings = $albumModel->getSettings();
                $this->em->persist($albumModel);
                if ($albumSettings instanceof Settings) {
                    $this->em->persist($albumSettings);
                }
                $this->em->flush();
            }
        }
    }
}
