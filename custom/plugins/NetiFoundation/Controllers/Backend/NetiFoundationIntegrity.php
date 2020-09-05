<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   shopware_beta
 * @author     hrombach
 */

class Shopware_Controllers_Backend_NetiFoundationIntegrity extends Enlight_Controller_Action implements \Shopware\Components\CSRFWhitelistAware
{
    public function indexAction()
    {
        /*
         * only relevant when checked out from git, please ignore if you downloaded from community store
         * The index.tpl file will be generated by npm, overwriting any default I could put in to let people know
         * they have to use npm. Sadly, there is no good way in git to lock files to a known state.
        */
        $index = $this->container->getParameter('neti_foundation.plugin_dir') .
            '/Views/backend/neti_foundation_integrity/' .
            'index.tpl';

        if (!is_file($index)) {
            $this->Front()->Plugins()->ViewRenderer()->setNoRender();

            echo <<<HTML
<p>If you're seeing this page, you have to:</p>
<ol>
    <li>Make sure you have npm installed.</li>
    <li>Go to "custom/plugins/NetiFoundation/Views/backend/neti_foundation_integrity/_resources/js/foundation-integrity"</li>
    <li>Run "npm install"</li>
    <li>Run "npm run build"</li>
</ol>
HTML;

            return;
        }

        $snippets = $this->container->get('snippets')->getNamespace('plugins/neti_foundation/backend/plugin_list');
        $swConfig = [
            'urls' => ['getPluginList' => $this->front->Router()->assemble(['action' => 'getPluginList'])],
            'snippets' => [
                'status_modified' => $snippets->get('status_modified', 'Modified'),
                'status_unmodified' => $snippets->get('status_unmodified', 'OK'),
                'status_missing' => $snippets->get('status_missing', 'Checksums not found'),
            ],
        ];

        $this->View()->assign('swConfig', json_encode($swConfig));

        $frontendURI = $this->Front()->Router()->assemble([
            'module' => 'frontend',
            'controller' => 'index',
        ]);

        $path = \rtrim(\parse_url($frontendURI, \PHP_URL_PATH), '/');

        $this->View()->assign('shopRoot', $path);
    }

    public function getPluginListAction()
    {
        $this->Front()->Plugins()->Json()->setRenderer();

        $plugins = $this->container->get('neti_foundation.service.plugin_integrity_check')->checkAll();

        $this->view->assign(['success' => true, 'pluginList' => $plugins]);
    }

    /**
     * Returns a list with actions which should not be validated for CSRF protection
     *
     * @return string[]
     */
    public function getWhitelistedCSRFActions()
    {
        return ['index', 'getPluginList'];
    }
}