<?php
/**
 * Copyright (c) Kickbyte GmbH - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

namespace KibVariantListing\Setup;


use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;

class Setup
{
    //todo add helptext and according translations (also for labels) to attributes
    /**
     * @var CrudService
     */
    private $crudService;
    /**
     * @var ModelManager
     */
    private $em;

    public function __construct(
        CrudService $crudService,
        ModelManager $em
    )
    {
        $this->crudService = $crudService;
        $this->em = $em;
    }

    /**
     * @param InstallContext $installContext
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function install(InstallContext $installContext)
    {
        $this->installAttributeColumns();

        $this->setDefaultValues();
    }

    /**
     * @param UpdateContext $updateContext
     * @throws \Exception
     */
    public function update(UpdateContext $updateContext)
    {
        if (version_compare($updateContext->getCurrentVersion(), '2.1.0', '<')) {
            $this->installAttributeColumns();

            $this->setDefaultValues();
        }

        if (version_compare($updateContext->getCurrentVersion(), '2.2.0', '<')) {
            $this->crudService->update('s_articles_img_attributes', 'kib_variantlisting_prop_img_mapping', 'multi_selection', [
                'label' => 'Filter Mapping',
                'supportText' => '',
                'helpText' => '',

                //user has the opportunity to translate the attribute field for each shop
                'translatable' => false,

                //attribute will be displayed in the backend module
                'displayInBackend' => true,

                //numeric position for the backend view, sorted ascending
                'position' => 100,

                //user can modify the attribute in the free text field module
                'custom' => false,

                //in case of multi_selection or single_selection type, article entities can be selected,
                'entity' => 'Shopware\Models\Property\Value',
            ], null, false);

            $this->em->generateAttributeModels(['s_articles_img_attributes']);
        }

        if (version_compare($updateContext->getCurrentVersion(), '2.3.0', '<')) {
            $this->crudService->update('s_articles_attributes', 'kib_variantlisting_img', 'single_selection', [
                'label' => 'Variant-Preview Image',
                'supportText' => '',
                'helpText' => 'Overrides image mapping rules',

                //user has the opportunity to translate the attribute field for each shop
                'translatable' => false,

                //attribute will be displayed in the backend module
                'displayInBackend' => true,

                //numeric position for the backend view, sorted ascending
                'position' => 100,

                //user can modify the attribute in the free text field module
                'custom' => false,

                //in case of multi_selection or single_selection type, article entities can be selected,
                'entity' => 'Shopware\Models\Media\Media',
            ], null, false);

            $this->crudService->update('s_categories_attributes', 'kib_variantlisting_viewDropdown', 'combobox', [
                'label' => 'Als Dropdown anzeigen',
                'supportText' => '',
                'helpText' => '',

                //user has the opportunity to translate the attribute field for each shop
                'translatable' => false,

                //attribute will be displayed in the backend module
                'displayInBackend' => true,

                //numeric position for the backend view, sorted ascending
                'position' => 100,

                //user can modify the attribute in the free text field module
                'custom' => false,

                'arrayStore' => [
                    ['key' => '0', 'value' => 'Deaktiviert'],
                    ['key' => '1', 'value' => 'Aktiviert'],
                    ['key' => '2', 'value' => 'Globale Einstellung']
                ],
            ], null, false, '2');

            $this->em->generateAttributeModels(['s_articles_attributes', 's_categories_attributes']);
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function setDefaultValues()
    {
        $this->em->getConnection()->exec('
              INSERT INTO
                s_categories_attributes (categoryID, kib_variantListing, kib_variantListing_slideout, kib_variantListing_textVariants, kib_variantlisting_zoomCover, kib_variantlisting_viewDropdown)
              SELECT
                c.id,
                \'2\',
                \'2\',
                \'2\',
                \'2\',
                \'2\'
              FROM s_categories c
                LEFT JOIN
                s_categories_attributes ca
                  ON c.id = ca.categoryID
              WHERE ca.id IS NULL
        ');

        $this->em->getConnection()->exec('
            UPDATE s_categories_attributes ca SET ca.kib_variantListing = \'2\', ca.kib_variantListing_slideout = \'2\', ca.kib_variantlisting_viewDropdown = \'2\', ca.kib_variantListing_textVariants = \'2\', ca.kib_variantlisting_zoomCover = \'2\'
        ');
    }

    /**
     * @param UninstallContext $uninstallContext
     * @throws \Exception
     */
    public function uninstall(UninstallContext $uninstallContext)
    {
        if (!$uninstallContext->keepUserData()) {
            $crudService = $this->crudService;

            $crudService->delete('s_categories_attributes', 'kib_variantListing', false);
            $crudService->delete('s_categories_attributes', 'kib_variantListing_slideout', false);
            $crudService->delete('s_categories_attributes', 'kib_variantListing_textVariants', false);
            $crudService->delete('s_categories_attributes', 'kib_variantlisting_zoomCover', false);
            $crudService->delete('s_categories_attributes', 'kib_variantlisting_viewDropdown', false);
            $crudService->delete('s_articles_img_attributes', 'kib_variantlisting_prop_img_mapping', false);
            $crudService->delete('s_articles_attributes', 'kib_variantlisting_img', false);

            $this->em->generateAttributeModels(['s_categories_attributes', 's_articles_img_attributes', 's_articles_attributes']);
        }
    }

    /**
     * @throws \Exception
     */
    private function installAttributeColumns()
    {
        $crudService = $this->crudService;

        $crudService->update('s_categories_attributes', 'kib_variantListing', 'combobox', [
            'label' => 'Varianten-Vorschau anzeigen',
            'supportText' => '',
            'helpText' => '',

            //user has the opportunity to translate the attribute field for each shop
            'translatable' => false,

            //attribute will be displayed in the backend module
            'displayInBackend' => true,

            //numeric position for the backend view, sorted ascending
            'position' => 100,

            //user can modify the attribute in the free text field module
            'custom' => false,

            'arrayStore' => [
                ['key' => '0', 'value' => 'Deaktiviert'],
                ['key' => '1', 'value' => 'Aktiviert'],
                ['key' => '2', 'value' => 'Globale Einstellung']
            ],
        ], null, false, '2');

        $crudService->update('s_categories_attributes', 'kib_variantListing_slideout', 'combobox', [
            'label' => 'Varianten-Vorschau bei Mouseover anzeigen',
            'supportText' => '',
            'helpText' => '',

            //user has the opportunity to translate the attribute field for each shop
            'translatable' => false,

            //attribute will be displayed in the backend module
            'displayInBackend' => true,

            //numeric position for the backend view, sorted ascending
            'position' => 100,

            //user can modify the attribute in the free text field module
            'custom' => false,

            'arrayStore' => [
                ['key' => '0', 'value' => 'Deaktiviert'],
                ['key' => '1', 'value' => 'Aktiviert'],
                ['key' => '2', 'value' => 'Globale Einstellung']
            ],
        ], null, false, '2');

        $crudService->update('s_categories_attributes', 'kib_variantListing_textVariants', 'combobox', [
            'label' => 'Varianten mit Textauswahl anzeigen',
            'supportText' => '',
            'helpText' => '',

            //user has the opportunity to translate the attribute field for each shop
            'translatable' => false,

            //attribute will be displayed in the backend module
            'displayInBackend' => true,

            //numeric position for the backend view, sorted ascending
            'position' => 100,

            //user can modify the attribute in the free text field module
            'custom' => false,

            'arrayStore' => [
                ['key' => '0', 'value' => 'Deaktiviert'],
                ['key' => '1', 'value' => 'Aktiviert'],
                ['key' => '2', 'value' => 'Globale Einstellung']
            ],
        ], null, false, '2');

        $crudService->update('s_categories_attributes', 'kib_variantlisting_zoomCover', 'combobox', [
            'label' => 'Bei Produkten ohne Varianten Vorschau vergrößern',
            'supportText' => '',
            'helpText' => '',

            //user has the opportunity to translate the attribute field for each shop
            'translatable' => false,

            //attribute will be displayed in the backend module
            'displayInBackend' => true,

            //numeric position for the backend view, sorted ascending
            'position' => 100,

            //user can modify the attribute in the free text field module
            'custom' => false,

            'arrayStore' => [
                ['key' => '0', 'value' => 'Deaktiviert'],
                ['key' => '1', 'value' => 'Aktiviert'],
                ['key' => '2', 'value' => 'Globale Einstellung']
            ],
        ], null, false, '2');

        $crudService->update('s_categories_attributes', 'kib_variantlisting_viewDropdown', 'combobox', [
            'label' => 'Als Dropdown anzeigen',
            'supportText' => '',
            'helpText' => '',

            //user has the opportunity to translate the attribute field for each shop
            'translatable' => false,

            //attribute will be displayed in the backend module
            'displayInBackend' => true,

            //numeric position for the backend view, sorted ascending
            'position' => 100,

            //user can modify the attribute in the free text field module
            'custom' => false,

            'arrayStore' => [
                ['key' => '0', 'value' => 'Deaktiviert'],
                ['key' => '1', 'value' => 'Aktiviert'],
                ['key' => '2', 'value' => 'Globale Einstellung']
            ],
        ], null, false,'2');

        $crudService->update('s_articles_img_attributes', 'kib_variantlisting_prop_img_mapping', 'multi_selection', [
            'label' => 'Filter Mapping',
            'supportText' => '',
            'helpText' => '',

            //user has the opportunity to translate the attribute field for each shop
            'translatable' => false,

            //attribute will be displayed in the backend module
            'displayInBackend' => true,

            //numeric position for the backend view, sorted ascending
            'position' => 100,

            //user can modify the attribute in the free text field module
            'custom' => false,

            //in case of multi_selection or single_selection type, article entities can be selected,
            'entity' => 'Shopware\Models\Property\Value',
        ], null, false);

        $this->crudService->update('s_articles_attributes', 'kib_variantlisting_img', 'single_selection', [
            'label' => 'Variant-Preview Image',
            'supportText' => '',
            'helpText' => 'Overrides image mapping rules',

            //user has the opportunity to translate the attribute field for each shop
            'translatable' => false,

            //attribute will be displayed in the backend module
            'displayInBackend' => true,

            //numeric position for the backend view, sorted ascending
            'position' => 100,

            //user can modify the attribute in the free text field module
            'custom' => false,

            //in case of multi_selection or single_selection type, article entities can be selected,
            'entity' => 'Shopware\Models\Media\Media',
        ], null, false);

        $this->em->generateAttributeModels(['s_categories_attributes', 's_articles_img_attributes', 's_articles_attributes']);
    }
}
