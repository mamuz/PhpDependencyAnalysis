<?php

namespace PhpDA\Writer;

use PhpDA\Entity\Collection;
use PhpDA\Feature\WriterInterface;
use PhpDA\Plugin\LoaderAwareTrait;

class Adapter implements WriterInterface
{
    use LoaderAwareTrait;

    /** @var Collection */
    private $collection;

    /** @var string */
    private $format = 'txt';

    public function __construct()
    {
        $this->collection = new Collection;
    }

    public function write(Collection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    public function to($format)
    {
        $this->format = $format;
        return $this;
    }

    public function in($file)
    {
        file_put_contents($file, $this->createContent());
        return $this;
    }

    /**
     * @return string
     */
    private function createContent()
    {
        $strategy = $this->getPluginLoader()->getWriteStrategyFor($this->format);
        return $strategy->filter($this->collection);
    }
}
