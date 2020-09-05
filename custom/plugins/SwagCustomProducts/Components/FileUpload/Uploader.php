<?php

/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace SwagCustomProducts\Components\FileUpload;

use Shopware\Components\Model\ModelManager;
use Shopware\Components\Thumbnail\Manager;
use Shopware\Models\Attribute\Media as MediaAttribute;
use Shopware\Models\Media\Album;
use Shopware\Models\Media\Media;
use SwagCustomProducts\Bootstrap\Installer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader implements UploaderInterface
{
    const FRONTEND_USER_UPLOAD_ALBUM = Installer::FRONTEND_UPLOAD_ALBUM;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var FileTypeWhitelistInterface
     */
    private $fileTypeWhitelist;

    /**
     * @var Manager
     */
    private $thumbnailManager;

    public function __construct(
        ModelManager $modelManager,
        FileTypeWhitelistInterface $fileTypeWhitelist,
        Manager $thumbnailManager
    ) {
        $this->modelManager = $modelManager;
        $this->fileTypeWhitelist = $fileTypeWhitelist;
        $this->thumbnailManager = $thumbnailManager;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(UploadedFile $uploadedFile)
    {
        $media = $this->createMediaModel($uploadedFile);

        $this->modelManager->persist($media);
        $this->modelManager->flush($media);

        if ($media->getType() === Media::TYPE_IMAGE) {
            $this->thumbnailManager->createMediaThumbnail($media);
        }

        return $media;
    }

    /**
     * @return Media
     */
    private function createMediaModel(UploadedFile $uploadedFile)
    {
        $media = new Media();
        $mediaType = $this->fileTypeWhitelist->getMediaOverrideType($uploadedFile->getClientOriginalExtension());

        $media->setAlbum($this->getAlbum());
        $media->setFile($uploadedFile);
        $media->setDescription($uploadedFile->getClientOriginalName());
        $media->setCreated(new \DateTime());
        $media->setUserId(0);

        $newFileName = $this->hashFileName($media);
        $media->setName(pathinfo($newFileName, PATHINFO_FILENAME)); //Gets the filename without extension
        $media->setPath('media/image/' . $newFileName);

        if ($mediaType !== null) {
            //Shopware may recognize several file types as images, even though it can not generate thumbnails
            //for it. Therefore, we set the type manually here to avoid the thumbnail generation.
            $media->setType($mediaType);
        }

        $mediaAttributes = new MediaAttribute();
        $media->setAttribute($mediaAttributes);

        return $media;
    }

    /**
     * @return Album|null
     */
    private function getAlbum()
    {
        /** @var \Shopware\Models\Media\Repository $mediaRepository */
        $mediaRepository = $this->modelManager->getRepository(Album::class);

        return $mediaRepository->findOneBy(['name' => self::FRONTEND_USER_UPLOAD_ALBUM]);
    }

    /**
     * @return string
     */
    private function hashFileName(Media $media)
    {
        return uniqid($media->getName(), true) . '.' . $media->getExtension();
    }
}
