<?php

namespace PhpDA\Feature;

use PhpDA\Entity\Collection;

interface WriteFilterInterface
{
    /**
     * @param Collection $collection
     * @return string
     */
    public function filter(Collection $collection);
}
