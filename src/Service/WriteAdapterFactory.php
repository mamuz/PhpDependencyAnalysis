<?php

namespace PhpDA\Service;

use PhpDA\Plugin\Loader;
use PhpDA\Writer\Adapter;

class WriteAdapterFactory implements FactoryInterface
{
    /**
     * @return Adapter
     */
    public function create()
    {
        return new Adapter(new Loader);
    }
}
