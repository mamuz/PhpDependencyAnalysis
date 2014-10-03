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
     * @param string $fqn
     * @return AdapterInterface
     */
    public function with($fqn);

    /**
     * @param string $file
     * @return AdapterInterface
     */
    public function to($file);
}
