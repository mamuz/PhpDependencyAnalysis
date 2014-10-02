<?php

namespace PhpDA\Entity;

use PhpParser\Node;

class Analysis
{
    /** @var string */
    private $parseError = '';

    /** @var Node\Name[] */
    private $namespaces = array();

    /** @var Node\Expr\Variable[] */
    private $superglobals = array();

    /** @var Node\Expr\Include_[] */
    private $includes = array();

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
     * @param Node\Name $namespace
     */
    public function addNamespace(Node\Name $namespace)
    {
        $this->namespaces[$namespace->toString()] = $namespace;
    }

    /**
     * @param Node\Expr\Variable $var
     */
    public function addSuperglobal(Node\Expr\Variable $var)
    {
        $this->superglobals[] = $var;
    }

    /**
     * @param Node\Expr\Include_ $include
     */
    public function addInclude(Node\Expr\Include_ $include)
    {
        $this->includes[] = $include;
    }
}
