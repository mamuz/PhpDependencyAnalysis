<?php

namespace PhpDA\Entity;

use PhpParser\Node;

class Analysis
{
    /** @var Node[] */
    private $stmts = array();

    /** @var string */
    private $parseError = '';

    /**
     * @param string $message
     */
    public function setParseError($message)
    {
        $this->parseError = (string) $message;
    }

    /**
     * @return string
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
        return !empty($this->parseError);
    }

    /**
     * @param Node $stmt
     */
    public function addStmt(Node $stmt)
    {
        $this->stmts[] = $stmt;
    }
}
