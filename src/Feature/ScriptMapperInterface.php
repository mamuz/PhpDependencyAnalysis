<?php

namespace PhpDA\Feature;

use PhpDA\Entity\Script;

interface ScriptMapperInterface
{
    /**
     * @param array $stmts
     * @return ScriptMapperInterface
     */
    public function populate(array $stmts);

    /**
     * @param Script $script
     * @return ScriptMapperInterface
     */
    public function to(Script $script);
}
