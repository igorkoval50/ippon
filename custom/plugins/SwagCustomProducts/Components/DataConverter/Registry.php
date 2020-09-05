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

namespace SwagCustomProducts\Components\DataConverter;

class Registry implements RegistryInterface
{
    const DEFAULT_CONVERTER = 'default';

    /**
     * @var ConverterInterface[] Array indexed by option type names
     */
    private $converters;

    /**
     * @param \IteratorAggregate $converterInterfaces
     */
    public function __construct($converterInterfaces)
    {
        $this->converters = $converterInterfaces;
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, ConverterInterface $instance)
    {
        $this->converters[$name] = $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        /** @var ConverterInterface $converter */
        foreach ($this->converters as $converter) {
            if ($converter->supports($name)) {
                return $converter;
            }
        }

        return $this->get(self::DEFAULT_CONVERTER);
    }
}
