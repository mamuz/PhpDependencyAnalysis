<?php

namespace PhpDA\Plugin;

use PhpDA\Feature\LoaderInterface;
use PhpDA\Feature\WriteFilterInterface;

class Loader implements LoaderInterface
{
    public function getWriteFilterFor($name)
    {
        $fqn = 'PhpDA\\Writer\\Filter\\' . ucfirst($name);

        if (!class_exists($fqn)) {
            throw new \RuntimeException('Filter for ' . $name . ' does not exist');
        }

        $formatter = new $fqn;

        if (!$formatter instanceof WriteFilterInterface) {
            throw new \RuntimeException('Filter ' . $fqn . ' is not instance of WriteFilterInterface');
        }

        return $formatter;
    }
}
