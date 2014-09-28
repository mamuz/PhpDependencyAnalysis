<?php

namespace PhpDA\Feature;

use PhpDA\Entity\Collection;

interface WriterInterface
{
    /**
     * @param Collection $collection
     * @return WriterInterface
     */
    public function write(Collection $collection);

    /**
     * @param $format
     * @return WriterInterface
     */
    public function to($format);

    /**
     * @param $file
     * @return WriterInterface
     */
    public function in($file);
}
