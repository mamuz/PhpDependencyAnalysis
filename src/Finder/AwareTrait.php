<?php

namespace PhpDA\Finder;

use Symfony\Component\Finder\Finder;

trait AwareTrait
{
    /** @var Finder */
    private $finder;

    /**
     * @param Finder $finder
     * @return void
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @return Finder
     */
    public function getFinder()
    {
        if (!$this->finder instanceof Finder) {
            $this->setFinder(new Finder);
        }

        return $this->finder;
    }
}
