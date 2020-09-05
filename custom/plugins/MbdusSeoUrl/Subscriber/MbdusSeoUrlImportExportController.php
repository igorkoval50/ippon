<?php
namespace MbdusSeoUrl\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MbdusSeoUrlImportExportController implements SubscriberInterface
{
	/**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'Enlight_Controller_Dispatcher_ControllerPath_Backend_MbdusSeoUrlImportExport' => 'onGetControllerPathBackend'
		);
	}
	
	/**
	 * Return the controllerpath of this plugin
	 *
	 * @return string
	 */
	public function onGetControllerPathBackend(\Enlight_Event_EventArgs $args) {
		$this->container->get('snippets')->addConfigDir($this->getPluginPath() . '/Resources/snippets/');
		return $this->getPluginPath() . '/Controllers/Backend/MbdusSeoUrlImportExport.php';
	}
	
	/**
	 * @return string
	 */
	public function getPluginPath()
	{
		return $this->container->getParameter('mbdus_seo_url.plugin_dir');
	}
}