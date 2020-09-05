<?php

namespace ArboroGoogleTracking\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Models\Category\Category;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseSubscriber
 * @package ArboroGoogleTracking\Subscriber
 */
abstract class AbstractSubscriber implements SubscriberInterface
{
    const EVENT = '';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $pluginConfig;

    /**
     * @var \Enlight_View_Default $view
     */
    protected $view;

    /**
     * BaseSubscriber constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->pluginConfig = array();
        try {
            $shop = $this->container->get('shop');
            $this->pluginConfig = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('ArboroGoogleTracking', $shop);
        }catch(\Exception $e) {

        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            static::EVENT => 'onDispatch',
        ];
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    protected function getConfigElement($name)
    {
        return array_key_exists($name, $this->pluginConfig) ? $this->pluginConfig[$name] : null;
    }

    /**
     * @return bool
     */
    protected function isTrackingConfigured()
    {
        $id = $this->getConfigElement('trackingID');

        return (null !== $id && $id !== '');
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    protected function shouldTrack($name)
    {
        return $this->isTrackingConfigured() && $this->getConfigElement($name);
    }

    /**
     * @return bool|string
     */
    protected function getTrackingType()
    {
        $id = $this->getConfigElement('trackingID');
        if(strpos($id, 'GTM') !== false) {
            return 'GTM';
        }

        if(strpos($id, 'UA') !== false) {
            return 'UA';
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function isTagManager()
    {
        return $this->getTrackingType() === 'GTM';
    }

    /**
     * @return bool
     */
    protected function isAnalytics()
    {
        return $this->getTrackingType() === 'UA';
    }

    /**
     * Helper function to switch between Shopware version related templates.
     *
     * @param string                     $template
     * @param \Enlight_View_Default|null $view
     *
     * @return AbstractSubscriber $this
     *
     * @throws \InvalidArgumentException
     */
    protected function addTemplateToView($template = null, $view = null)
    {
        if(null === $view) {
            $view = $this->view;
        }

        try {
            $view->addTemplateDir(
                $this->container->getParameter('arboro_google_tracking.plugin_dir') . '/Resources/views/'
            );

            if(null !== $template) {
                $view->extendsTemplate('frontend/' . $template);
            }
        } catch(\Exception $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

        return $this;
    }

    /**
     * Helper function to assign multiple variables and values to given view.
     *
     * @param array|string               $variables
     * @param mixed                      $value
     * @param \Enlight_View_Default|null $view
     *
     * @return AbstractSubscriber $this
     *
     * @throws \InvalidArgumentException
     */
    protected function assign($variables, $value = null, $view = null)
    {
        if(null === $view) {
            $view = $this->view;
        }

        if(is_array($variables)) {
            foreach($variables as $variable) {
                $view->$variable = $this->getConfigElement($variable);
            }
        } else {
            if(null === $variables || $variables === '') {
                throw new \InvalidArgumentException('$variables can not be null or empty!');
            }

            if($value !== null) {
                $view->$variables = $value;
            } else {
                $view->$variables = $this->getConfigElement($variables);
            }
        }

        return $this;
    }

    /**
     * Get maximal 5 parent categories as string for given child category.
     *
     * If the amount of parent categories is smaller than or equals 5 the last
     * element (e.g. language name) will be dropped.
     *
     * @param Category $category
     *
     * @return string
     */
    protected function getParentCategoryPath(Category $category)
    {
        $result = [];
        $result[] = $category->getName();
        $pathElements = array_filter(explode('|', $category->getPath()));
        $i = 0;

        foreach($pathElements as $element) {
            if($i >= 4) {
                break;
            }

            if(null !== $element && '' !== $element) {
                $parent = Shopware()
                    ->Models()
                    ->getRepository(Category::class)
                    ->find($element);
                if(null !== $parent) {
                    $result[] = $parent->getName();
                }
            }

            $i++;
        }

        if(count($pathElements) <= 5) {
            array_pop($result);
        }

        return implode('/', array_reverse($result));
    }

    /**
     * Helper function to check wether or not the plugin is licensed.
     * @return bool
     */
    protected function isLicensed()
    {
        try {
            return $this->container->getParameter('arboro_google_tracking.plugin');
        } catch(\Exception $e) {
            return false;
        }
    }
}