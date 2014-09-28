<?php

namespace PhpDA\Entity;

class Script
{
    /** @var Doc */
    private $doc;

    /** @var Object[] */
    private $objects;

    /** @var Method[] */
    private $functions;

    /** @var IncludeStmt[] */
    private $includeStmts;

    /** @var Constant[] */
    private $consts;

    /** @var Stmt[] */
    private $stmts;

    /** @var GlobalVar[] */
    private $globalVars;

    /** @var string */
    private $error;

    /**
     * @param string $error
     * @return Script
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @param Stmt[] $stmts
     * @return Script
     */
    public function setStmts($stmts)
    {
        $this->stmts = $stmts;
        return $this;
    }
}
