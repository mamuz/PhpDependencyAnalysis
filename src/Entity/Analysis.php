<?php

namespace PhpDA\Entity;

use PhpParser\Error;
use PhpParser\Node;

class Analysis
{
    /** @var Error */
    private $parseError;

    /** @var Node[] */
    private $stmts = array();

    /**
     * @param Error $error
     */
    public function setParseError(Error $error)
    {
        $this->parseError = $error;
    }

    /**
     * @return Error|null
     */
    public function getParseError()
    {
        return $this->parseError;
    }

    /**
     * @return bool
     */
    public function hasParseError()
    {
        return $this->parseError instanceof Error;
    }

    /**
     * @param Node  $stmt
     * @param mixed $type
     */
    public function addStmt(Node $stmt, $type)
    {
        if (!array_key_exists($type, $this->stmts)) {
            $this->stmts[$type] = array();
        }

        $this->stmts[$type][] = $stmt;
    }
}
