<?php
/**
 * TAB10 / ENOOA s.r.o.
 *
 */

namespace TabtenListingHover\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs as EventArgs;
use Shopware\Components\Theme\LessDefinition;

class Theme implements SubscriberInterface
{
    /**
     * @var
     */
    private $pluginPath;

    /**
     * Theme constructor.
     *
     * @param $pluginPath
     */
    public function __construct($pluginPath)
    {
        $this->pluginPath = $pluginPath;
    }

    /*
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected' => 'onTemplateDirectoriesCollect',
            'Theme_Compiler_Collect_Plugin_Less' => 'onCollectLessFiles',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'onCollectJavaScriptFiles',
        ];
    }

    public function onTemplateDirectoriesCollect(EventArgs $args)
    {
        $dirs = $args->getReturn();

        $dirs[] = $this->pluginPath . '/Resources/views/';

        $args->setReturn($dirs);
    }

    public function onCollectLessFiles(\Enlight_Event_EventArgs $args)
    {

        $pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('TabtenListingHover');
        $pluginPath = Shopware()->Container()->getParameter('tabten_listing_hover.plugin_dir');

        // create array with all variables from plugin-config
        $variables = [];
        foreach ($pluginConfig as $key => $value) {
            $variables[$key] = $value;
        }

        $less = new LessDefinition(
            $variables,
            array(
                $pluginPath . '/Resources/views/frontend/_public/src/less/all.less',
            ),
            $pluginPath . '/Resources/views/frontend/_public/src/less/'
        );

        return new ArrayCollection([$less]);
    }

    /**
     * Provides the file collection for js files
     *
     * @return ArrayCollection
     */
    public function onCollectJavaScriptFiles()
    {
        $jsFiles = [
            $this->pluginPath . '/Resources/views/frontend/_public/src/js/jquery.listinghover.js',
        ];

        return new ArrayCollection($jsFiles);
    }
}
