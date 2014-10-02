<?php

namespace PhpDA\Service;

use PhpDA\Writer\Adapter;
use PhpDA\Writer\Loader;

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
