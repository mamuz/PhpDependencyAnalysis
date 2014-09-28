<?php

namespace PhpDA\Feature;

interface LoaderInterface
{
    /**
     * @param string $name
     * @throws \RuntimeException
     * @return WriteFilterInterface
     */
    public function getWriteFilterFor($name);
}
