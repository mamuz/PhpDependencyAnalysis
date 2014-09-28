<?php

namespace PhpDA\Plugin;

use PhpDA\Feature\LoaderInterface;
use PhpDA\Feature\WriteStrategyInterface;

class Loader implements LoaderInterface
{
    public function getWriteStrategyFor($name)
    {
        $fqn = 'PhpDA\\Writer\\Strategy\\' . ucfirst($name);

        if (!class_exists($fqn)) {
            throw new \RuntimeException('Strategy for ' . $name . ' does not exist');
        }

        $formatter = new $fqn;

        if (!$formatter instanceof WriteStrategyInterface) {
            throw new \RuntimeException(
                'Strategy ' . $fqn . ' is not an instance of WriteStrategyInterface'
            );
        }

        return $formatter;
    }
}
