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

namespace SwagCustomProducts\Components\GarbageCollection;

use DateTime;
use Doctrine\DBAL\Connection;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\CachedConfigReader;
use Shopware\Models\Media\Media;
use SwagCustomProducts\Components\Types\Types\FileUploadType;
use SwagCustomProducts\Components\Types\Types\ImageUploadType;

class GarbageCollector implements GarbageCollectorInterface
{
    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var string
     */
    private $configurationAvailabilityDays;

    /**
     * @param string $pluginName
     */
    public function __construct(
        CachedConfigReader $config,
        ModelManager $modelManager,
        $pluginName
    ) {
        $this->modelManager = $modelManager;
        $configArray = $config->getByPluginName($pluginName);
        $this->configurationAvailabilityDays = $configArray['configurationAvailabilityDays'];
    }

    /**
     * {@inheritdoc}
     */
    public function cleanUp()
    {
        $expiredDate = new DateTime('-' . $this->configurationAvailabilityDays . ' day');

        $configurations = $this->getExpiredConfigurations($expiredDate);

        $this->deleteMedia($configurations);

        $ids = array_column($configurations, 'id');

        $query = $this->modelManager->getConnection()->createQueryBuilder();
        $query->delete('s_plugin_custom_products_configuration_hash')
            ->where('id IN (:ids)')
            ->setParameter(':ids', $ids, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    /**
     * @param int   $id
     * @param array $options
     *
     * @return bool
     */
    private function isMediaOption($id, $options)
    {
        foreach ($options as $option) {
            if ($option['id'] != $id) {
                continue;
            }

            return in_array($option['type'], [FileUploadType::TYPE, ImageUploadType::TYPE], true);
        }

        return false;
    }

    /**
     * @return array
     */
    private function getExpiredConfigurations(DateTime $expirationDate)
    {
        $builder = $this->modelManager->getConnection()->createQueryBuilder();

        $builder->select('configs.*')
            ->from('s_plugin_custom_products_configuration_hash', 'configs')
            ->where('permanent = 0')
            ->andWhere('created_at < :dateForDeletion')
            ->setParameter('dateForDeletion', $expirationDate->format('Y-m-d H:i:s'));

        return $builder->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param array $configurations
     */
    private function deleteMedia($configurations)
    {
        $mediaIds = $this->extractMediaIds($configurations);

        if (count($mediaIds) === 0) {
            return;
        }

        foreach ($mediaIds as $id) {
            $media = $this->modelManager->find(Media::class, $id);
            if ($media && !$this->isPermanent($media)) {
                $this->modelManager->remove($media);
                $this->modelManager->flush($media);
            }
        }
    }

    /**
     * @param array[] $configurations
     *
     * @return array
     */
    private function extractMediaIds($configurations)
    {
        $ids = [];
        foreach ($configurations as $config) {
            $template = json_decode($config['template'], true);
            $values = json_decode($config['configuration'], true);

            foreach ($values as $optionId => $value) {
                if (!$this->isMediaOption($optionId, $template)) {
                    continue 2;
                }

                if (is_array($value)) {
                    $value = $value[0];
                }

                $temp = json_decode($value, true);
                $ids = array_merge($ids, array_column($temp, 'id'));
            }
        }

        return array_keys(array_flip($ids));
    }

    /**
     * @return bool
     */
    private function isPermanent(Media $media)
    {
        return $media->getAttribute() && $media->getAttribute()->getSwagCustomProductsPermanent();
    }
}
