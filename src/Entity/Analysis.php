<?php

namespace PhpDA\Entity;

use PhpParser\Error;
use PhpParser\Node;

class Analysis
{
    /** @var Error */
    private $parseError;

    /** @var Node\Name[] */
    private $namespaces = array();

    /** @var Node\Expr\Variable[] */
    private $superglobals = array();

    /** @var Node\Expr\Include_[] */
    private $includes = array();

    /** @var Node\Expr[] */
    private $unsupportedStmts = array();

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
     * @param Node\Name $namespace
     */
    public function addNamespace(Node\Name $namespace)
    {
        $this->namespaces[] = $namespace;
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

    /**
     * - $$x
     * - new $x
     * - $x::$y
     * - $x()
     * - DI.get('FQN/Alias')
     *
     * @param Node\Expr $stmt
     */
    public function addUnsupportedStmt(Node\Expr $stmt)
    {
        $this->unsupportedStmts[] = $stmt;
    }
}
