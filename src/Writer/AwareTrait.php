<?php

namespace PhpDA\Writer;

use PhpDA\Feature\WriterInterface;

trait AwareTrait
{
    /** @var WriterInterface */
    private $writer;

    /**
     * @param WriterInterface $writer
     * @return void
     */
    public function setWriter(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @return WriterInterface
     */
    public function getWriter()
    {
        if (!$this->writer instanceof WriterInterface) {
            $this->setWriter(new Adapter);
        }

        return $this->writer;
    }
}
