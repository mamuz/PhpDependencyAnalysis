<?php

namespace PhpDA\Parser\Mapper;

use PhpDA\Entity;
use PhpDA\Feature\ScriptMapperInterface;

class Script implements ScriptMapperInterface
{
    /** @var array */
    private $stmts = array();

    public function populate(array $stmts)
    {
        $this->stmts = $stmts;
        return $this;
    }

    public function to(Entity\Script $script)
    {
        // @todo
        $script->setStmts($this->stmts);
        return $this;
    }
}
