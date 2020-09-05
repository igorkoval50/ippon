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

namespace SwagCustomProducts\Components\Services;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use SwagCustomProducts\Components\Types\Types\FileUploadType;
use SwagCustomProducts\Components\Types\Types\ImageUploadType;

class HashManager implements HashManagerInterface
{
    const CONFIG_HASH_TABLE = 's_plugin_custom_products_configuration_hash';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var DateTimeService
     */
    private $dateTimeService;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    public function __construct(Connection $connection, DateTimeServiceInterface $dateTimeService, ContextServiceInterface $contextService)
    {
        $this->connection = $connection;
        $this->dateTimeService = $dateTimeService;
        $this->contextService = $contextService;
    }

    /**
     * {@inheritdoc}
     */
    public function manageHashByConfiguration(array $configuration, $permanent = false, array $options = [])
    {
        $configuration['shopId'] = $this->contextService->getShopContext()->getShop()->getId();

        $createdAt = $this->dateTimeService->getNowString(DateTimeServiceInterface::YMD_HIS);
        $jsonConfiguration = $this->createJsonFromConfiguration($configuration, $permanent, $createdAt);

        $hash = md5($jsonConfiguration);
        $savedConfiguration = $this->findConfigurationByHash($hash);

        if (!$permanent && is_array($savedConfiguration)) {
            return $hash;
        }

        $this->saveConfiguration($hash, $jsonConfiguration, $createdAt, $permanent, $options);

        $types = array_column($options, 'type');
        $isValidType = in_array(FileUploadType::TYPE, $types) || in_array(ImageUploadType::TYPE, $types);

        if ($isValidType && $permanent === true) {
            $this->setMediaPermanentFlag($configuration, $options);
        }

        return $hash;
    }

    /**
     * {@inheritdoc}
     */
    public function findConfigurationByHash($configurationHash)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select(['configuration'])
            ->from(self::CONFIG_HASH_TABLE, 'hashTable')
            ->where('hashTable.hash = ?')
            ->setParameters([$configurationHash]);

        $jsonConfig = $queryBuilder->execute()->fetch(\PDO::FETCH_COLUMN);

        return json_decode($jsonConfig, true);
    }

    /**
     * {@inheritdoc}
     */
    public function createHash(array $configuration)
    {
        $json = $this->createJsonFromConfiguration($configuration, false, '');

        return md5($json);
    }

    /**
     * create a JSON string from the configuration array.
     * If permanent the createdAt param would add to the configuration array.
     *
     * @param bool   $permanent
     * @param string $createdAt
     *
     * @return string
     */
    private function createJsonFromConfiguration(array $configuration, $permanent, $createdAt)
    {
        if ($permanent) {
            $configuration['custom_product_created_at'] = $createdAt;
        }

        return json_encode($configuration);
    }

    /**
     * Saves the configuration hash in the given table
     *
     * @param string $hash
     * @param string $jsonConfiguration
     * @param string $createdAt
     * @param bool   $permanent
     * @param array  $options
     *
     * @return string
     */
    private function saveConfiguration($hash, $jsonConfiguration, $createdAt, $permanent, $options)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->insert(self::CONFIG_HASH_TABLE)
            ->values([
                'hash' => '?',
                'configuration' => '?',
                'permanent' => '?',
                'created_at' => '?',
                'template' => '?',
            ])
            ->setParameters([
                $hash,
                $jsonConfiguration,
                $permanent,
                $createdAt,
                json_encode($options),
            ]);

        $queryBuilder->execute();

        return $this->connection->lastInsertId();
    }

    /**
     * Updates all files with a permanent flag.
     *
     * @param array $templateOptions - All options of the used template
     */
    private function setMediaPermanentFlag(array $customerConfiguration, array $templateOptions)
    {
        $mediaIds = $this->getConfiguredMediaIds($customerConfiguration, $templateOptions);

        $builder = $this->connection->createQueryBuilder();
        $builder->update('s_media_attributes')
            ->set('swag_custom_products_permanent', '1')
            ->where('mediaID IN (:mediaIds)')
            ->setParameter('mediaIds', $mediaIds, Connection::PARAM_INT_ARRAY);

        $builder->execute();
    }

    /**
     * @param array $customerConfiguration
     * @param array $templateOptions       - All options of the used template
     *
     * @return int[]
     */
    private function getConfiguredMediaIds($customerConfiguration, $templateOptions)
    {
        $mediaOptions = $this->getMediaOptionsFromTemplate($templateOptions);

        return $this->getMediaIdsFromConfiguration($customerConfiguration, $mediaOptions);
    }

    /**
     * @param array $templateOptions - All options of the used template
     *
     * @return array
     */
    private function getMediaOptionsFromTemplate(array $templateOptions)
    {
        $mediaOptions = array_filter($templateOptions, function ($templateOption) {
            return in_array($templateOption['type'], [FileUploadType::TYPE, ImageUploadType::TYPE], true);
        });

        return $mediaOptions;
    }

    /**
     * Returns all media ids which are used by the given configuration.
     *
     * @param array $customerConfiguration - Customer configuration
     * @param array $mediaOptions          - Array of options with a media files
     *
     * @return int[]
     */
    private function getMediaIdsFromConfiguration(array $customerConfiguration, array $mediaOptions)
    {
        $configuredMediaIds = [];
        foreach ($mediaOptions as $mediaOption) {
            $mediaOptionId = $mediaOption['id'];
            if (empty($customerConfiguration[$mediaOptionId])) {
                continue;
            }

            $configuredMedia = json_decode($customerConfiguration[$mediaOption['id']][0], true);
            $configuredMediaIds = array_merge($configuredMediaIds, array_column($configuredMedia, 'id'));
        }

        return $configuredMediaIds;
    }
}
