<?php

namespace PhpDA\Entity;

class Analysis
{
    /** @var \PhpParser\Node[] */
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
     * @param \PhpParser\Node[] $stmts
     */
    public function setStmts(array $stmts)
    {
        $this->stmts = $stmts;
    }
}
