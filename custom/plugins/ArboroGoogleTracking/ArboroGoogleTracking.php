<?php

namespace ArboroGoogleTracking;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ArboroGoogleTracking
 * @package ArboroGoogleTracking
 */
class ArboroGoogleTracking extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'CookieCollector_Collect_Cookies' => 'addGoogleAnalyticsCookie'
        ];
    }

    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context)
    {
        $this->checkForIncompatiblePlugins();
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
    }

    /**
     * @param UpdateContext $context
     */
    public function update(UpdateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
        parent::uninstall($context);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('arboro_google_tracking.plugin_dir', $this->getPath());

        if($this->isLicenseCheckEnabled()) {
            $container->setParameter(
                'arboro_google_tracking.plugin',
                true
            ); //TODO: refactor $this->checkLicense(false));
        } else {
            $container->setParameter('arboro_google_tracking.plugin', true);
        }

        parent::build($container);
    }

    private function checkForIncompatiblePlugins()
    {
        $sql = 'SELECT 1 FROM s_core_plugins WHERE name = ? AND active = 1';
        $db = $this->container->get('dbal_connection');

        $assertStandard = $db->fetchColumn($sql, ['SwagGoogle']);
        if($assertStandard) {
            throw new \Exception('The default Shopware Google Services plugin has to be deactivated and uninstalled.');
        }

        $assertAnalytics = $db->fetchColumn($sql, ['ArboroGoogleAnalytics']);
        if($assertAnalytics) {
            throw new \Exception(
                'The legacy plugin arboro Google Analytics has to be deactivated and uninstalled.'
            );
        }
    }

    /**
     * Helper function for shopwares checkLicense function to prevent license checks in local
     * (docker) development environments.
     *
     * @return bool
     */
    private function isLicenseCheckEnabled()
    {
        return true;
    }

    /**
     * @return \Shopware\Bundle\CookieBundle\CookieCollection
     */
    public function addGoogleAnalyticsCookie()
    {
        $config = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName(
            'ArboroGoogleTracking',
            $this->container->get('shop')
        );

        $collection = new \Shopware\Bundle\CookieBundle\CookieCollection();

        if($config['enableCookieConsent'] === 'swcct') {
            $collection->add(new \Shopware\Bundle\CookieBundle\Structs\CookieStruct(
                    '_ga',
                    '/(^_g(a|at|id)$)|AMP_TOKEN|^_gac_.*$/',
                    'Google Analytics',
                    \Shopware\Bundle\CookieBundle\Structs\CookieGroupStruct::STATISTICS
                )
            );
        }
        return $collection;
    }
}
