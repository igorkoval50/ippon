<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Subscriber;

use Doctrine\ORM\NonUniqueResultException;
use Enlight\Event\SubscriberInterface;
use NetiFoundation\Service;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Config\Form;
use Shopware\Models\Form\Repository;
use Shopware\Models\Plugin\Plugin;

/**
 * Class PluginManager
 *
 * @package NetiFoundation\Subscriber
 */
class PluginManager implements SubscriberInterface
{
    /**
     * @var Service\PluginManager
     */
    private $pluginManager;

    /**
     * @var ModelManager
     */
    private $em;

    /**
     * @var Service\PluginManager\Base
     */
    private $pluginManagerBase;

    /**
     * @var Service\PluginManager\Form
     */
    private $pluginManagerForm;

    /**
     * PluginManager constructor.
     *
     * @param Service\PluginManager      $pluginManager
     * @param ModelManager               $em
     * @param Service\PluginManager\Base $pluginManagerBase
     * @param Service\PluginManager\Form $pluginManagerForm
     */
    public function __construct(
        Service\PluginManager $pluginManager,
        ModelManager $em,
        Service\PluginManager\Base $pluginManagerBase,
        Service\PluginManager\Form $pluginManagerForm
    ) {
        $this->pluginManager     = $pluginManager;
        $this->em                = $em;
        $this->pluginManagerBase = $pluginManagerBase;
        $this->pluginManagerForm = $pluginManagerForm;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Controllers_Backend_Config::getFormAction::before' => 'getFormAction',
        ];
    }

    /**
     * @param \Enlight_Hook_HookArgs $args
     *
     * @throws NonUniqueResultException
     */
    public function getFormAction(\Enlight_Hook_HookArgs $args): void
    {
        /**
         * @var \Shopware_Controllers_Backend_Config    $subject
         * @var \Enlight_Controller_Request_RequestHttp $request
         */
        $subject = $args->getSubject();
        $request = $subject->Request();
        /** @var Repository $repository */
        $repository = $this->em->getRepository(Form::class);
        $qbr        = $repository->createQueryBuilder('form');

        $qbr->join('form.plugin', 'plugin');
        $qbr->addFilter((array)$request->getParam('filter', []));
        $model = $qbr->getQuery()->getOneOrNullResult();

        if ($model instanceof Form) {
            $plugin = $model->getPlugin();

            if (!$plugin instanceof Plugin) {
                return;
            }

            if ('ShopwarePlugins' !== $plugin->getNamespace()) {
                return;
            }

            $plugin = $this->pluginManagerBase->getPluginByName($plugin->getName());

            if ($this->pluginManager->checkRequiredPlugin($plugin)) {
                $configFile = $this->pluginManagerBase->getConfigFile($plugin);
                $form       = $configFile->get('form');
                $this->pluginManagerForm->updateForm($plugin, $form);
            }
        }
    }
}
