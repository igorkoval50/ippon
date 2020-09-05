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

namespace SwagNewsletter\Components;

use Shopware\Components\Model\ModelManager;
use SwagNewsletter\Models\Component;

class NewsletterComponentHelper implements NewsletterComponentHelperInterface
{
    /**
     * @var array
     */
    protected $components = [];

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @param ModelManager $modelManager
     */
    public function __construct(
        ModelManager $modelManager
    ) {
        $this->modelManager = $modelManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function createNewsletterComponent(array $options, $pluginId)
    {
        if (empty($options['template'])) {
            throw new \RuntimeException('"template" cannot be empty');
        }

        $config = array_merge(
            [
                'convertFunction' => null,
                'description' => '',
                'cls' => '',
                'xtype' => 'newsletter-components-base',
            ],
            $options
        );

        $repository = $this->modelManager->getRepository(Component::class);

        $component = $repository->findOneBy(
            [
                'pluginId' => $pluginId,
                'template' => $config['template'],
            ]
        );

        if (!$component) {
            $component = new Component();
        }

        $component->fromArray($config);

        $component->setPluginId($pluginId);

        $this->components[] = $component;

        return $component;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        foreach ($this->components as $component) {
            $this->modelManager->persist($component);
        }

        $this->modelManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findByPluginId($pluginId)
    {
        $repository = $this->modelManager->getRepository(Component::class);

        return $repository->findBy(['pluginId' => $pluginId]);
    }
}
