<?php

namespace PhpDA\Feature;

use PhpDA\Entity\Collection;

interface WriteStrategyInterface
{
    /**
     * @param Collection $collection
     * @return string
     */
    public function filter(Collection $collection);
}
