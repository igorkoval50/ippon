<?php

declare(strict_types=1);

/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\PluginManager;

use NetiFoundation\Struct\PluginConfigFile\Formfield;
use Shopware\Components\{Model\ModelManager, Plugin, Plugin\FormSynchronizer};
use Shopware\Models\Plugin\Plugin as PluginModel;

/**
 * Class Form
 *
 * @package NetiFoundation\Service\PluginManager
 */
class Form implements FormInterface
{
    /**
     * @var ModelManager
     */
    protected $em;

    /**
     * Constructor for the Form service
     * Logging is not required, because the actual work is done by Shopware.
     * Also, the form is being updated every time it is getting opened in the Backend,
     * so we would have too much logging overhead.
     *
     * @param ModelManager $em
     */
    public function __construct(ModelManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Plugin           $plugin
     * @param Formfield[]|null $form
     */
    public function installForm(Plugin $plugin, array $form = null): void
    {
        $this->updateForm($plugin, $form);
    }

    /**
     * @param Plugin           $plugin
     * @param Formfield[]|null $form
     */
    public function updateForm(Plugin $plugin, array $form = null): void
    {
        if (is_array($form)) {
            $this->updateFormFromConfigFile($plugin, $form);
        }
    }

    /**
     * @param Plugin      $plugin
     * @param Formfield[] $form
     */
    protected function updateFormFromConfigFile(Plugin $plugin, array $form): void
    {
        $pluginModel = $this->em->getRepository(PluginModel::class)->findOneBy(['name' => $plugin->getName()]);

        if (!$pluginModel instanceof PluginModel) {
            return;
        }

        $elements = [];
        $position = 0;

        foreach ($form as $element) {
            $element                        = $element->toArray();
            $element['options']['position'] = ++$position;
            $elements[]                     = $element;
        }

        $config = [
            'elements' => $elements,
        ];

        $formSynchronizer = new FormSynchronizer($this->em);
        $formSynchronizer->synchronize($pluginModel, $config);
    }
}
