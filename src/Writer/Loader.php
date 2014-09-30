<?php

namespace PhpDA\Writer;

use PhpDA\Writer\Strategy\FilterInterface;

class Loader implements LoaderInterface
{
    public function getStrategyFor($name)
    {
        $fqn = 'Writer\\Strategy\\' . ucfirst($name);

        if (!class_exists($fqn)) {
            throw new \RuntimeException('Strategy for ' . $name . ' does not exist');
        }

        $formatter = new $fqn;

        if (!$formatter instanceof FilterInterface) {
            throw new \RuntimeException(
                'Strategy ' . $fqn . ' is not an instance of WriteStrategyInterface'
            );
        }

        return $formatter;
    }
}
