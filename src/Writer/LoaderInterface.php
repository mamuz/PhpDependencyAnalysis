<?php

namespace PhpDA\Writer;

interface LoaderInterface
{
    /**
     * @param string $name
     * @throws \RuntimeException
     * @return \PhpDA\Writer\Strategy\StrategyInterface
     */
    public function getStrategyFor($name);
}
