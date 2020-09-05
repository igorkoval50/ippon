<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components;

use Exception;
use Shopware\Models\Snippet\Snippet;
use Shopware\Models\Snippet\SnippetRepository;

class Updater extends InstallManager
{
    /**
     * Runs the updater.
     *
     * @param string $oldVersion
     * @return bool
     */
    public function run($oldVersion)
    {
        $success = [];

        $success[] = $this->createConfigForm();

        if (version_compare($oldVersion, '1.3.1', '<=')) {
            $success[] = $this->to140();
        }

        return $this->isSuccess($success);
    }

    /**
     * Updating to version 1.4.0 with migrating old dirty snippets to new namespace.
     *
     * @return bool
     */
    private function to140()
    {
        $container = $this->plugin->Application()->Container();
        /**
         * Need to import the new snippets first, to make the transition without exceptions
         */
        try {
            $container->get('shopware.snippet_database_handler')->loadToDatabase($this->plugin->Path() . 'Snippets/');
        } catch (Exception $e) {
            //
        }

        /**
         * Migrating old snippets to new namespace
         *
         * Examples:
         *
         * old: namespace: plugins/mediameetsFacebookPixel name: frontend/close
         * new: namespace: frontend/plugins/mediameetsFacebookPixel/notification name: close
         *
         * old: namespace: plugins/mediameetsFacebookPixel name: frontend/opt-in/button
         * new: namespace: frontend/plugins/mediameetsFacebookPixel/notification name: opt-in/button
         */
        $oldSnippetNamespace = 'plugins/mediameetsFacebookPixel';
        $newSnippetNamespace = 'frontend/' . $oldSnippetNamespace . '/notification';

        try {
            $modelManager = $container->get('models');
            /** @var SnippetRepository $snippetRepository */
            $snippetRepository = $modelManager->getRepository('Shopware\Models\Snippet\Snippet');
            $queryBuilder = $snippetRepository->createQueryBuilder('s');

            /**
             * Select all dirty old namespaced snippets
             */
            $dirtyOldSnippets = $snippetRepository->createQueryBuilder('s')
                ->where('s.namespace = :namespace')
                ->setParameter('namespace', $oldSnippetNamespace)
                ->andWhere('s.dirty = 1')
                ->getQuery()
                ->execute();

            $snippetsMigrated = false;

            /**
             * Migrate dirty old snippets to new namespace
             */
            if (count($dirtyOldSnippets) > 0) {

                /* @var $snippet Snippet */
                foreach ($dirtyOldSnippets as $snippet) {
                    $oldNameParts = explode('/', $snippet->getName());
                    unset($oldNameParts[0]);
                    $newName = implode('/', $oldNameParts);

                    if (! empty($newName)) {
                        $snippetRepository->createQueryBuilder('s')
                            ->update('Shopware\Models\Snippet\Snippet', 's')
                            ->set('s.value', $queryBuilder->expr()->literal($snippet->getValue()))
                            ->set('s.dirty', 1)
                            ->where('s.namespace = :namespace')
                            ->andWhere('s.name = :name')
                            ->andWhere('s.shopId = :shopId')
                            ->andWhere('s.localeId = :localeId')
                            ->setParameters([
                                'namespace' => $newSnippetNamespace,
                                'name' => $newName,
                                'shopId' => $snippet->getShopId(),
                                'localeId' => $snippet->getLocaleId()
                            ])
                            ->getQuery()
                            ->execute();
                    }
                }

                $snippetsMigrated = true;
            }

            /**
             * Delete old namespace when no dirty old snippets exist or all snippets were migrated
             */
            if (count($dirtyOldSnippets) == 0 || $snippetsMigrated) {
                $snippetRepository->createQueryBuilder('s')
                    ->delete('Shopware\Models\Snippet\Snippet', 's')
                    ->where('s.namespace = :namespace')
                    ->setParameter('namespace', $oldSnippetNamespace)
                    ->getQuery()
                    ->execute();
            }
        } catch (Exception $e) {
            //
        }

        return true;
    }
}
