<?php

namespace PhpDA\Writer\Strategy;

interface FactoryInterface
{
    /**
     * @return StrategyInterface
     */
    public function create();
}
