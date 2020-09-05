<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiFoundation\Backports\ViewAutoloader\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Plugin\Plugin;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ViewAutoloaderSubscriber implements SubscriberInterface
{
    /**
     * @var array
     */
    private $templateDirs;

    /**
     * @var ModelManager
     */
    private $em;

    /**
     * @var \Enlight_Event_EventManager
     */
    private $events;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(
        array $templateDirs,
        ModelManager $em,
        \Enlight_Event_EventManager $events,
        ContainerInterface $container
    )
    {
        $this->templateDirs = $templateDirs;
        $this->em           = $em;
        $this->events       = $events;
        $this->container    = $container;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected' => 'injectTemplates',
        ];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     *
     * @return mixed
     * @throws \Enlight_Exception
     */
    public function injectTemplates(\Enlight_Event_EventArgs $args)
    {
        $return        = $args->getReturn();
        $activePlugins = \array_column(
            $this->em->createQueryBuilder()
                ->from(Plugin::class, 'plugin')
                ->select('plugin.name')
                ->where('plugin.active = true AND plugin.name LIKE \'Neti%\'')
                ->getQuery()
                ->getArrayResult(),
            'name',
            'name'
        );

        foreach (\array_intersect_key($this->templateDirs, $activePlugins) as $plugin => $dirsPerPlugin) {
            $skip = $this->events->notifyUntil(
                'NetiFoundation_ViewAutoloader_addViews_' . $plugin,
                new \Enlight_Event_EventArgs([
                    'subject' => $this,
                    'shop'    => $this->container->get(
                        'shop',
                        ContainerInterface::NULL_ON_INVALID_REFERENCE
                    ),
                ])
            );

            if ($skip) {
                continue;
            }

            \array_push($return, ...$dirsPerPlugin);
        }

        return $return;
    }
}