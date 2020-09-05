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

use Shopware\Models\Media\Media;
use Shopware_Components_Snippet_Manager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileUploadService implements FileUploadServiceInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Shopware_Components_Snippet_Manager
     */
    private $snippetManager;

    /**
     * @var UploaderInterface
     */
    private $uploader;

    /**
     * @var FileSizeFormatterInterface
     */
    private $fileSizeFormatter;

    /**
     * @var FileTypeWhitelistInterface
     */
    private $fileTypeWhitelist;

    public function __construct(
        ValidatorInterface $validatorInterface,
        Shopware_Components_Snippet_Manager $snippetManager,
        UploaderInterface $uploader,
        FileSizeFormatterInterface $fileSizeFormatter,
        FileTypeWhitelistInterface $fileTypeWhitelist
    ) {
        $this->validator = $validatorInterface;
        $this->snippetManager = $snippetManager;
        $this->uploader = $uploader;
        $this->fileSizeFormatter = $fileSizeFormatter;
        $this->fileTypeWhitelist = $fileTypeWhitelist;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(FileBag $fileBag)
    {
        /** @var Media[] $uploadedMedia */
        $uploadedMedia = [];

        /** @var UploadedFile[] $uploadedFiles */
        foreach ($fileBag as $uploadedFiles) {
            foreach ($uploadedFiles as $uploadedFile) {
                $uploadedMedia[] = $this->uploader->upload($uploadedFile);
            }
        }

        return $uploadedMedia;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(FileBag $fileBag, FileConfigStruct $fileConfigStruct)
    {
        $this->validateMaxFiles($fileBag, $fileConfigStruct);

        $errors = [];

        /** @var UploadedFile[] $uploadedFiles */
        foreach ($fileBag as $uploadedFiles) {
            foreach ($uploadedFiles as $uploadedFile) {
                $constraint = $this->createFileConstraint($fileConfigStruct, $uploadedFile);

                /** @var ConstraintViolationList $violations */
                $violations = $this->validator->validate($this->getFilePath($uploadedFile), $constraint);

                /** @var ConstraintViolationInterface $violation */
                foreach ($violations->getIterator() as $violation) {
                    $errors[] = new FileUploadError(
                        $violation->getMessage(),
                        $uploadedFile->getClientOriginalName()
                    );
                }
            }
        }

        if (!empty($errors)) {
            throw new FileUploadException($errors);
        }
    }

    /**
     * @return File
     */
    private function createFileConstraint(FileConfigStruct $fileConfigStruct, UploadedFile $uploadedFile)
    {
        return new File([
            'maxSize' => (int) $fileConfigStruct->getMaxSize(),
            'mimeTypes' => $this->fileTypeWhitelist->getMimeTypeWhitelist($fileConfigStruct->getType()),
            'maxSizeMessage' => sprintf(
                $this->getErrorMessage('detail/validate/file_size'),
                $uploadedFile->getClientOriginalName(),
                $this->fileSizeFormatter->formatBytes($fileConfigStruct->getMaxSize())
            ),
            'mimeTypesMessage' => sprintf(
                $this->getErrorMessage('detail/validate/file_type'),
                $uploadedFile->getClientOriginalName(),
                implode(', ', $this->fileTypeWhitelist->getExtensionWhitelist($fileConfigStruct->getType()))
            ),
        ]);
    }

    /**
     * @throws MaxFilesException
     */
    private function validateMaxFiles(FileBag $fileBag, FileConfigStruct $fileConfigStruct)
    {
        $fileAmount = $this->countUploadedFiles($fileBag);

        if ($fileAmount > $fileConfigStruct->getMaxFiles()) {
            throw new MaxFilesException($this->getMaxFileErrorMessage($fileConfigStruct));
        }
    }

    /**
     * @return int
     */
    private function countUploadedFiles(FileBag $fileBag)
    {
        $fileCounter = 0;
        foreach ($fileBag as $uploadedFiles) {
            $fileCounter += count($uploadedFiles);
        }

        return $fileCounter;
    }

    /**
     * @param string $snippet
     *
     * @return string
     */
    private function getErrorMessage($snippet)
    {
        return $this->snippetManager->getNamespace('frontend/detail/option')->get($snippet);
    }

    /**
     * @return string
     */
    private function getFilePath(UploadedFile $uploadedFile)
    {
        return $uploadedFile->getPath() . '/' . $uploadedFile->getFilename();
    }

    /**
     * @return string
     */
    private function getMaxFileErrorMessage(FileConfigStruct $fileConfigStruct)
    {
        $errorMessage = $this->getErrorMessage('detail/validate/max_files');

        return sprintf($errorMessage, $fileConfigStruct->getMaxFiles());
    }
}
