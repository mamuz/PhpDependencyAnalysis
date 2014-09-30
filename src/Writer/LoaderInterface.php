<?php

namespace PhpDA\Writer;

interface LoaderInterface
{
    /**
     * @param string $name
     * @throws \RuntimeException
     * @return \PhpDA\Writer\Strategy\FilterInterface
     */
    public function getStrategyFor($name);
}
