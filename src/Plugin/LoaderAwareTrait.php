<?php

namespace PhpDA\Plugin;

use PhpDA\Feature\LoaderInterface;

trait LoaderAwareTrait
{
    /** @var LoaderInterface */
    private $pluginLoader;

    /**
     * @param LoaderInterface $pluginLoader
     * @return void
     */
    public function setPluginLoader(LoaderInterface $pluginLoader)
    {
        $this->pluginLoader = $pluginLoader;
    }

    /**
     * @return LoaderInterface
     */
    public function getPluginLoader()
    {
        if (!$this->pluginLoader instanceof Loader) {
            $this->setPluginLoader(new Loader);
        }

        return $this->pluginLoader;
    }
}
