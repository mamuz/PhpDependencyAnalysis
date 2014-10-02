<?php

namespace PhpDA\Service;

use Symfony\Component\Finder\Finder;

class FinderFactory implements FactoryInterface
{
    /**
     * @return Finder
     */
    public function create()
    {
        return new Finder;
    }
}
