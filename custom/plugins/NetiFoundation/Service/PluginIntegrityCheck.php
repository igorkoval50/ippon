<?php /** @noinspection PhpIncludeInspection */

/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   shopware_beta
 * @author     hrombach
 */

namespace NetiFoundation\Service;

use Shopware\Components\Plugin\XmlReader\XmlPluginReader;
use Symfony\Component\Finder\Finder;

class PluginIntegrityCheck
{
    /**
     * @var string
     */
    private $pluginsPath;

    /**
     * @var XmlPluginReader
     */
    private $infoReader;

    public function __construct(string $pluginsPath, XmlPluginReader $infoReader)
    {
        $this->pluginsPath = $pluginsPath;
        $this->infoReader  = $infoReader;
    }

    public function checkAll(): array
    {
        $finder = new Finder();

        $finder->in($this->pluginsPath)->directories()->name('Neti*')->depth(0);

        $plugins = ['missing' => [], 'modified' => [], 'unmodified' => []];
        foreach ($finder as $path => $pluginDir) {
            $checksumFile = $path . '/md5checksum.php';

            try {
                $pluginInfo    = $this->infoReader->read($path . '/plugin.xml');
                $pluginVersion = $pluginInfo['version'] ?? 'error';
            } catch (\InvalidArgumentException $e) {
                $pluginVersion = 'error';
            }

            if (!\is_file($checksumFile) && !\is_readable($checksumFile)) {
                $plugins['missing'][] = ['name' => $pluginDir->getBasename(), 'version' => $pluginVersion];
                continue;
            }

            $fileList = include $checksumFile;

            unset($fileList['__SECRET__'], $fileList['md5checksum.php']);

            $changedFiles = [];
            foreach ($fileList as $file => $checksum) {
                if (\md5_file($path . \DIRECTORY_SEPARATOR . $file) !== $checksum) {
                    $changedFiles[] = $file;
                }
            }

            if (\count($changedFiles) > 0) {
                $plugins['modified'][] = [
                    'name'    => $pluginDir->getBasename(),
                    'files'   => $changedFiles,
                    'version' => $pluginVersion,
                ];
            } else {
                $plugins['unmodified'][] = ['name' => $pluginDir->getBasename(), 'version' => $pluginVersion];
            }
        }

        return $plugins;
    }
}