<?php

namespace PhpDA\Feature;

interface LoaderInterface
{
    /**
     * @param string $name
     * @throws \RuntimeException
     * @return WriteStrategyInterface
     */
    public function getWriteStrategyFor($name);
}
