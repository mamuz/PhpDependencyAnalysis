<?php

namespace PhpDA\Feature;

interface ParserInterface
{
    /**
     * @param string $code
     * @return mixed
     */
    public function analyze($code);
}
