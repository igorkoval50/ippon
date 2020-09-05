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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use PDO;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Models\Article\Article;
use Shopware\Models\Media\Media;
use Shopware\Models\Shop\Repository;
use Shopware\Models\Shop\Shop;
use SwagCustomProducts\Components\Types\TypeFactoryInterface;
use SwagCustomProducts\Models\Option;
use SwagCustomProducts\Models\Template;
use SwagCustomProducts\Models\Value;

/**
 * @codeCoverageIgnore
 */
class Migration implements MigrationInterface
{
    const OLD_PLUGIN_NAME = 'SwagCustomizing';

    const OLD_PLUGIN_TABLE_GROUPS = '`s_plugin_customizing_groups`';
    const OLD_PLUGIN_TABLE_TYPES = '`s_plugin_customizing_types`';
    const OLD_PLUGIN_TABLE_OPTIONS = '`s_plugin_customizing_options`';
    const OLD_PLUGIN_TABLE_VALUES = '`s_plugin_customizing_values`';
    const OLD_PLUGIN_TABLE_ARTICLES = '`s_plugin_customizing_articles`';
    const OLD_PLUGIN_TABLE_CHARGE_VALUES = '`s_plugin_customizing_charge_values`';
    const OLD_PLUGIN_TABLE_CHARGE_ITEMS = '`s_plugin_customizing_charge_items`';

    const OLD_PLUGIN_TRANSLATION_TYPE_GROUP = 'customizing-group';
    const OLD_PLUGIN_TRANSLATION_TYPE_OPTION = 'customizing-option';
    const OLD_PLUGIN_TRANSLATION_TYPE_VALUE = 'customizing-value';

    const NEW_TRANSLATION_TYPE_TEMPLATE = 'customProductTemplateTranslations';
    const NEW_TRANSLATION_TYPE_OPTION = 'customProductOptionTranslations';
    const NEW_TRANSLATION_TYPE_VALUE = 'customProductValueTranslations';

    const NEW_PLUGIN_TABLE_ARTICLE_RELATION = 's_plugin_custom_products_template_product_relation';

    const CORE_PLUGINS_TABLE = 's_core_plugins';
    const CORE_MEDIA_TABLE = 's_media';
    const CORE_CONFIG_TABLE = 's_core_config_values';
    const CORE_TRANSLATION_TABLE = 's_core_translations';
    const CORE_CONFIG_ELEMENTS_TABLE = 's_core_config_elements';
    const CORE_SHOPS_TABLE = 's_core_shops';

    /**
     * @var array
     */
    private $errorLog = [];

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->connection = $container->get('dbal_connection');
    }

    /**
     * {@inheritdoc}
     */
    public function isMigrationPossible()
    {
        if ($this->checkHideButton()) {
            return false;
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        // At first we check if SwagCustomizing is installed / present.
        $result = $queryBuilder->select('*')
            ->from(self::CORE_PLUGINS_TABLE)
            ->where('name = :oldPluginName')
            ->setParameter('oldPluginName', self::OLD_PLUGIN_NAME)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        // If SwagCustomizing is installed / present
        // check if all necessary database tables are installed.
        if (!$this->checkIfTablesExists()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups()
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('*')
            ->from(self::OLD_PLUGIN_TABLE_GROUPS)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function saveHideMigrationButton($value)
    {
        $id = $this->getHideButtonConfigId();
        $value = serialize((bool) $value);

        if (!$id) {
            return false;
        }

        if (!$this->checkHideButton()) {
            $shopId = $this->getDefaultShopId();
            $queryBuilder = $this->connection->createQueryBuilder();

            return $queryBuilder->insert(self::CORE_CONFIG_TABLE)
                ->setValue('element_id', ':elementId')
                ->setValue('shop_id', ':shopId')
                ->setValue('value', ':value')
                ->setParameter('elementId', $id)
                ->setParameter('shopId', $shopId)
                ->setParameter('value', $value)
                ->execute();
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->update(self::CORE_CONFIG_TABLE)
            ->set('value', ':val')
            ->where('element_id = :elementId')
            ->setParameter('val', $value)
            ->setParameter('elementId', $id)
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function startMigration($groupId)
    {
        $this->errorLog = [];
        if (!$groupId) {
            throw new \RuntimeException(sprintf('"%s" is no groupId.', $groupId));
        }

        try {
            // Try to create a template from group
            $template = $this->createTemplateByGroupId($groupId);

            if (!$template) {
                throw new \RuntimeException(sprintf('No template created with groupId "%d"', $groupId));
            }

            $entityManger = $this->container->get('models');
            $entityManger->persist($template);
            $entityManger->flush($template);

            // try to create options by groupId
            $this->migrateOptionsByGroupId($groupId, $template);

            // after save we have a id, so we can migrate the translations
            $this->migrateTranslations(
                self::OLD_PLUGIN_TRANSLATION_TYPE_GROUP,
                $groupId,
                self::NEW_TRANSLATION_TYPE_TEMPLATE,
                $template->getId()
            );

            return true;
        } catch (\Exception $ex) {
            $this->log($ex->getMessage());

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorLog()
    {
        return $this->errorLog;
    }

    /**
     * Get the default shop Id
     *
     * @return int
     */
    private function getDefaultShopId()
    {
        /** @var Repository $shopRepo */
        $shopRepo = $this->container->get('models')->getRepository(Shop::class);

        return $shopRepo->getActiveDefault()->getId();
    }

    /**
     * Add a string to the log
     */
    private function log($string)
    {
        $this->errorLog[] = $string;
    }

    /**
     * @param string | integer $groupId
     *
     * @return Template|null
     */
    private function createTemplateByGroupId($groupId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $group = $queryBuilder->select('*')
            ->from(self::OLD_PLUGIN_TABLE_GROUPS)
            ->where('id = :id')
            ->setParameter('id', $groupId)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);

        if (!$group) {
            $this->log(sprintf('No group with id "%d" found.', $groupId));

            return null;
        }

        $template = new Template();
        $template->setDisplayName($group['name']);
        $template->setInternalName($this->createInternalName($group['name']));
        $template->setDescription($group['description']);
        $template->setActive(false);

        if ($group['image_path']) {
            $media = $this->findMediaByPath($group['image_path']);
            if (!$media) {
                $this->log(sprintf('The media with the image path "%s" was not found.', $group['image_path']));
            } else {
                $template->setMediaId($media->getId());
                $template->setMedia($media);
            }
        }

        $articles = $this->findArticlesByGroupId($groupId);

        if ($articles) {
            $template->setArticles(new ArrayCollection($articles));
        }

        return $template;
    }

    /**
     * @param string           $oldType
     * @param string | integer $oldId
     * @param string           $newType
     * @param string | integer $newId
     */
    private function migrateTranslations($oldType, $oldId, $newType, $newId)
    {
        if (!$oldType || !$oldId || !$newType || !$newId) {
            $this->log(
                sprintf(
                    'Translation cannot proceed with data: (oldType = %s) (oldId = %d) (newType = %s) (newId = %d)',
                    $oldType,
                    $oldId,
                    $newType,
                    $newId
                )
            );

            return;
        }

        $translations = $this->getTranslation($oldType, $oldId);

        if (!$translations) {
            return;
        }

        foreach ($translations as &$translation) {
            $translation['objecttype'] = $newType;
            $translation['objectkey'] = $newId;

            $objectData = $this->mapObjectData($translation);

            if ($objectData === null) {
                continue;
            }

            $translation['objectdata'] = $objectData;
            $this->saveTranslation($translation);
        }
    }

    private function mapObjectData(array $translation)
    {
        switch ($translation['objecttype']) {
            case self::NEW_TRANSLATION_TYPE_TEMPLATE:
                $oldTranslationValues = unserialize($translation['objectdata']);

                $newTranslationValues = [];
                foreach ($oldTranslationValues as $translationKey => $translationValue) {
                    switch ($translationKey) {
                        case 'name':
                            $newTranslationValues['displayName'] = $translationValue;
                            continue 2;
                        case 'description':
                            $newTranslationValues['description'] = $translationValue;
                            continue 2;
                        default:
                            $this->log(
                                sprintf(
                                    'Could not map translation with key: "%s" value: "%s" ',
                                    $translationKey,
                                    $translationValue
                                )
                            );
                    }
                }

                $newTranslationValues['description'] = $oldTranslationValues['description'];

                return serialize($newTranslationValues);

            case self::NEW_TRANSLATION_TYPE_OPTION:
                $oldTranslationValues = unserialize($translation['objectdata']);

                $newTranslationValues = [];

                foreach ($oldTranslationValues as $translationKey => $translationValue) {
                    switch ($translationKey) {
                        case 'emptyText':
                            $newTranslationValues['placeholder'] = $translationValue;
                            continue 2;
                        case 'name':
                            $newTranslationValues['name'] = $translationValue;
                            continue 2;
                        default:
                            $this->log(
                                sprintf(
                                    'Could not map translation with key: "%s" value: "%s" ',
                                    $translationKey,
                                    $translationValue
                                )
                            );
                    }
                }

                return serialize($newTranslationValues);

            case self::NEW_TRANSLATION_TYPE_VALUE:
                $oldTranslationValues = unserialize($translation['objectdata']);
                $newTranslationValues = [];

                foreach ($oldTranslationValues as $translationKey => $translationValue) {
                    switch ($translationKey) {
                        case 'description':
                            $newTranslationValues['name'] = $translationValue;
                            continue 2;
                        default:
                            $this->log(
                                sprintf(
                                    'Could not map translation with key: "%s" value: "%s" ',
                                    $translationKey,
                                    $translationValue
                                )
                            );
                    }
                }

                return serialize($newTranslationValues);

            default:
                return null;
        }
    }

    private function saveTranslation(array $translationData)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->insert(self::CORE_TRANSLATION_TABLE)
            ->values([
                'objecttype' => ':objecttype',
                'objectdata' => ':objectdata',
                'objectkey' => ':objectkey',
                'objectlanguage' => ':objectlanguage',
                'dirty' => ':dirty',
            ]);

        foreach ($translationData as $key => $value) {
            $queryBuilder->setParameter($key, $value);
        }

        $queryBuilder->execute();
    }

    /**
     * @param string           $type
     * @param string | integer $id
     *
     * @return array
     */
    private function getTranslation($type, $id)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('*')
            ->from(self::CORE_TRANSLATION_TABLE)
            ->where('objecttype = :type')
            ->andWhere('objectkey = :key')
            ->setParameter('type', $type)
            ->setParameter('key', $id)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new internal name by current timeStamp and the name,
     * because the internal name is unique
     *
     * @param string $name
     *
     * @return string
     */
    private function createInternalName($name)
    {
        /** @var DateTimeServiceInterface $dateTimeService */
        $dateTimeService = $this->container->get('custom_products.date_time_service');
        $suffix = md5($dateTimeService->getNowString() . $name);

        return $name . '_' . $suffix;
    }

    /**
     * @param int | string $groupId
     *
     * @return array|null
     */
    private function migrateOptionsByGroupId($groupId, Template $template)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $entityManger = $this->container->get('models');

        $options = $queryBuilder->select('*')
            ->from(self::OLD_PLUGIN_TABLE_OPTIONS)
            ->where('group_id = :groupId')
            ->setParameter('groupId', $groupId)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);

        if (!$options) {
            $this->log(sprintf('There a no Options in the groupId "%d" found', $groupId));

            return null;
        }

        $newOptions = [];

        foreach ($options as $option) {
            $newOption = new Option();
            $newOption->setName($option['name']);
            $newOption->setRequired($option['required']);
            $newOption->setPosition($option['position']);
            $newOption->setPlaceholder($option['empty_text']);
            $newOption->setMaxFiles($option['max_uploads'] ?: 1);
            $newOption->setMaxFileSize(3145728);
            $newOption->setCouldContainValues(false);
            $newOption->setTemplateId($template->getId());

            $type = $this->mapType($option['type_id']);

            if (!$type) {
                $this->log(sprintf('Could not map type with id "%d".', $option['type_id']));
                continue;
            }

            $newOption->setType($type);

            $prices = $this->createPrices($option['number']);

            if ($prices) {
                $newOption->setPrices(new ArrayCollection($prices));
            }

            // Now save the option to get a option ID
            $entityManger->persist($newOption);
            $entityManger->flush($newOption);

            if ($this->optionCouldContainValues($newOption->getType())) {
                $newOption->setCouldContainValues(true);
                $values = $this->findValuesByOptionIdAndOptionType(
                    $option['id'],
                    unserialize($option['default_value']),
                    $newOption->getType(),
                    $newOption
                );

                $newOption->setValues(new ArrayCollection($values));

                // Now save again for save the association
                $entityManger->persist($newOption);
                $entityManger->flush($newOption);
            }

            // after save we have a id, so we can migrate the translations
            $this->migrateTranslations(
                self::OLD_PLUGIN_TRANSLATION_TYPE_OPTION,
                $option['id'],
                self::NEW_TRANSLATION_TYPE_OPTION,
                $newOption->getId()
            );

            $newOptions[] = $newOption;
        }

        return $newOptions;
    }

    /**
     * @param string                  $orderNumber
     * @param null | integer | string $optionId
     * @param null | integer | string $valueId
     *
     * @return array
     */
    private function createPrices($orderNumber, $optionId = null, $valueId = null)
    {
        /** @var PriceFactoryInterface $priceFactory */
        $priceFactory = $this->container->get('custom_products.price_factory');
        if (!$orderNumber) {
            return $priceFactory->createDefaultPrice($optionId, $valueId);
        }

        $charge = $this->getChargeByOrderNumber($orderNumber);

        if (!$charge) {
            return $priceFactory->createDefaultPrice($optionId, $valueId);
        }

        return $priceFactory->createPricesFromCharge($charge, $optionId, $valueId);
    }

    /**
     * @param string $orderNumber
     *
     * @return mixed|null
     */
    private function getChargeByOrderNumber($orderNumber)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $charge = $queryBuilder->select('*')
            ->from(self::OLD_PLUGIN_TABLE_CHARGE_ITEMS)
            ->where('number LIKE :orderNumber')
            ->setParameter('orderNumber', $orderNumber)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);

        if (!$charge) {
            return null;
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $charge['items'] = $queryBuilder->select('*')
            ->from(self::OLD_PLUGIN_TABLE_CHARGE_VALUES)
            ->where('item_id = :chargeId')
            ->setParameter('chargeId', $charge['id'])
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);

        return $charge;
    }

    /**
     * @param int | string $optionId
     * @param int          $defaultValue
     * @param string       $optionType
     *
     * @return Value[]
     */
    private function findValuesByOptionIdAndOptionType($optionId, $defaultValue, $optionType, Option $newOption)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $entityManger = $this->container->get('models');

        $values = $queryBuilder->select('*')
            ->from(self::OLD_PLUGIN_TABLE_VALUES)
            ->where('option_id = :optionId')
            ->setParameter('optionId', $optionId)
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);

        if (!$values) {
            return null;
        }

        $newValues = [];
        foreach ($values as $value) {
            $newValue = new Value();
            $newValue->setName($value['description']);
            $newValue->setSeoTitle($value['description']);
            $newValue->setPosition($value['position']);
            $newValue->setOptionId($newOption->getId());

            if ($defaultValue == $value['id']) {
                $newValue->setIsDefaultValue(true);
            }

            // this is a simple fallback if no description specified.
            if (!$newValue->getName()) {
                $newValue->setName($value['value']);
            }

            $prices = $this->createPrices($value['number']);
            $newValue->setPrices(new ArrayCollection($prices));

            switch ($optionType) {
                case 'imageselect':
                    $media = $this->findMediaByPath($value['value']);
                    if ($media) {
                        $newValue->setMediaId($media->getId());
                        $newValue->setValue($value['value']);
                        break;
                    }
                    $this->log(
                        sprintf(
                            'The media with the path "%s" not found. Value: "%s"',
                            $value['value'],
                            $value['description']
                        )
                    );
                    $newValue->setValue($value['value']);
                    break;
                default:
                    $newValue->setValue($value['value']);
            }

            // Now save the newValue
            $entityManger->persist($newValue);
            $entityManger->flush($newValue);

            // after save we have a id, so we can migrate the translations
            $this->migrateTranslations(
                self::OLD_PLUGIN_TRANSLATION_TYPE_VALUE,
                $value['id'],
                self::NEW_TRANSLATION_TYPE_VALUE,
                $newValue->getId()
            );

            $newValues[] = $newValue;
        }

        return $newValues;
    }

    /**
     * @param string $typeName
     *
     * @return bool
     */
    private function optionCouldContainValues($typeName)
    {
        /** @var TypeFactoryInterface $typeFactory */
        $typeFactory = $this->container->get('custom_products.type_factory');
        foreach ($typeFactory->factory() as $type) {
            if ($type->getType() === $typeName) {
                return $type->couldContainValues();
            }
        }

        return false;
    }

    /**
     * @param int | string $typeId
     *
     * @return string|null
     */
    private function mapType($typeId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $type = $queryBuilder->select('*')
            ->from(self::OLD_PLUGIN_TABLE_TYPES)
            ->where('id = :typeId')
            ->setParameter('typeId', $typeId)
            ->execute()
            ->fetch();

        if (!$type) {
            return null;
        }

        switch ($type['type']) {
            case 'text':
            case 'text_field':
                return 'textfield';
            case 'upload':
            case 'upload_file':
                return 'fileupload';
            case 'text_area':
                return 'textarea';
            case 'text_html':
                return 'wysiwyg';
            case 'upload_image':
                return 'imageupload';
            case 'multiple':
                return 'multiselect';
            case 'image_select':
                return 'imageselect';
            case 'color_select':
                return 'colorselect';
            case 'select':
            case 'checkbox':
            case 'radio':
            case 'date':
            case 'time':
                return $type['type'];
            default:
                return null;
        }
    }

    /**
     * @param string | integer $groupId
     *
     * @return array
     */
    private function findArticlesByGroupId($groupId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $articleIds = $queryBuilder->select('article_id')
            ->from(self::OLD_PLUGIN_TABLE_ARTICLES)
            ->where('group_id = :groupId')
            ->setParameter('groupId', $groupId)
            ->execute()
            ->fetchAll(PDO::FETCH_COLUMN);

        $repository = $this->container->get('models');

        $articles = [];

        foreach ($articleIds as $articleId) {
            /** @var Article $article */
            $article = $repository->getReference(Article::class, (int) $articleId);

            if (!$article) {
                $this->log(sprintf('The product with the id %d could not found.', $articleId));
                continue;
            }

            if ($this->isCustomProduct($article->getId())) {
                $this->log(
                    sprintf(
                        'The product with the id %d could not add to template, because it is another CustomProduct',
                        $articleId
                    )
                );
                continue;
            }

            $articles[] = $article;
        }

        return $articles;
    }

    /**
     * @param int | string $articleId
     *
     * @return bool
     */
    private function isCustomProduct($articleId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $count = $queryBuilder->select('COUNT(article_id)')
            ->from(self::NEW_PLUGIN_TABLE_ARTICLE_RELATION)
            ->where('article_id = :articleId')
            ->setParameter('articleId', $articleId)
            ->execute()
            ->fetch(PDO::FETCH_COLUMN);

        if ($count) {
            return true;
        }

        return false;
    }

    /**
     * @param string $path
     *
     * @return Media|null
     */
    private function findMediaByPath($path)
    {
        if (!$path) {
            return null;
        }

        $imagePath = explode('/', $path);
        $imageName = $imagePath[count($imagePath) - 1];
        $imagePieces = explode('.', $imageName);

        if (count($imagePieces) !== 2) {
            return null;
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $imageId = $queryBuilder->select('id')
            ->from(self::CORE_MEDIA_TABLE)
            ->where('name = :imageName')
            ->setParameter('imageName', $imagePieces[0])
            ->execute()
            ->fetch(PDO::FETCH_COLUMN);

        if (!$imageId) {
            return null;
        }

        return $this->container->get('models')->getRepository(Media::class)->find($imageId);
    }

    /**
     * @return bool
     */
    private function checkHideButton()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $id = $this->getHideButtonConfigId();

        $value = $queryBuilder->select('value')
            ->from(self::CORE_CONFIG_TABLE)
            ->where('element_id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetch(PDO::FETCH_COLUMN);

        return unserialize($value);
    }

    private function getHideButtonConfigId()
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder->select('id')
            ->from(self::CORE_CONFIG_ELEMENTS_TABLE)
            ->where('name LIKE "doNotShowMigrationButton"')
            ->execute()
            ->fetch(PDO::FETCH_COLUMN);

        return $result;
    }

    /**
     * Check each Table in $tables array.
     *
     * @return bool
     */
    private function checkIfTablesExists()
    {
        $tables = [
            self::OLD_PLUGIN_TABLE_GROUPS,
            self::OLD_PLUGIN_TABLE_TYPES,
            self::OLD_PLUGIN_TABLE_OPTIONS,
            self::OLD_PLUGIN_TABLE_VALUES,
            self::OLD_PLUGIN_TABLE_ARTICLES,
            self::OLD_PLUGIN_TABLE_CHARGE_VALUES,
            self::OLD_PLUGIN_TABLE_CHARGE_ITEMS,
        ];

        try {
            foreach ($tables as $tableName) {
                $this->checkIfTableExists($tableName);
            }
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * Check one table.
     * If Table not exists doctrine throw a Exception that we catch
     * in the previous function "checkIfTablesExists".
     *
     * @param string $tableName
     */
    private function checkIfTableExists($tableName)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('*')
            ->from($tableName)
            ->setMaxResults(1)
            ->execute()
            ->fetch();
    }
}
