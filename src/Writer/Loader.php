<?php

namespace PhpDA\Writer;

use PhpDA\Writer\Strategy\FactoryInterface;
use PhpDA\Writer\Strategy\StrategyInterface;

class Loader implements LoaderInterface
{
    /** @var array */
    private $aliases = array(
        'txt' => 'PhpDA\\Writer\\Strategy\\Writer\\Strategy\\Txt',
    );

    /**
     * @param array $aliases
     */
    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    public function getStrategyFor($name)
    {
        $name = strtolower($name);
        if (!array_key_exists($name, $this->getAliases())) {
            throw new \RuntimeException('Strategy for ' . $name . ' is not registered');
        }

        $fqn = $this->getAliases()[$name];

        if (!class_exists($fqn)) {
            throw new \RuntimeException('Strategy for ' . $name . ' does not exist');
        }

        $class = new \ReflectionClass($fqn);
        if ($constructor = $class->getConstructor()) {
            if ($constructor->getNumberOfParameters()) {
                throw new \RuntimeException('Strategy ' . $name . ' is not invokable');
            }
        }

        $strategy = new $fqn;

        if ($strategy instanceof FactoryInterface) {
            $strategy = $strategy->create();
        }

        if (!$strategy instanceof StrategyInterface) {
            throw new \RuntimeException('Strategy ' . $fqn . ' is not an instance of StrategyInterface');
        }

        return $strategy;
    }
}
