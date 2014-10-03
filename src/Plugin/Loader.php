<?php

namespace PhpDA\Plugin;

use PhpDA\Service\FactoryInterface;

class Loader implements LoaderInterface
{
    public function get($fqn)
    {
        if (!class_exists($fqn)) {
            throw new \RuntimeException('Class for ' . $fqn . ' does not exist');
        }

        $class = new \ReflectionClass($fqn);
        if ($constructor = $class->getConstructor()) {
            if ($constructor->getNumberOfParameters()) {
                throw new \RuntimeException('Class ' . $fqn . ' must be creatable without arguments');
            }
        }

        $plugin = new $fqn;

        if ($plugin instanceof FactoryInterface) {
            $plugin = $plugin->create();
        }

        return $plugin;
    }
}
