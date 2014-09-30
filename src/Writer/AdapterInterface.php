<?php

namespace PhpDA\Writer;

use PhpDA\Entity\AnalysisCollection;

interface AdapterInterface
{
    /**
     * @param AnalysisCollection $analysisCollection
     * @return AdapterInterface
     */
    public function write(AnalysisCollection $analysisCollection);

    /**
     * @param string $format
     * @return AdapterInterface
     */
    public function to($format);

    /**
     * @param string $file
     * @return AdapterInterface
     */
    public function in($file);
}
