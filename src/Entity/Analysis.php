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
    private $shellExecs = array();

    /** @var Node\Expr[] */
    private $unsupportedStmts = array();

    /** @var Node\Scalar\String[] */
    private $namespacedStrings = array();

    /** @var Node\Expr\MethodCall[] */
    private $iocContainerAccessors = array();

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
     * @param Node\Expr $shellExec
     */
    public function addShellExec(Node\Expr $shellExec)
    {
        $this->shellExecs[] = $shellExec;
    }

    /**
     * @param Node\Expr $stmt
     */
    public function addUnsupportedStmt(Node\Expr $stmt)
    {
        $this->unsupportedStmts[] = $stmt;
    }

    /**
     * @param Node\Scalar\String $string
     */
    public function addNamespacedString(Node\Scalar\String $string)
    {
        $this->namespacedStrings[] = $string;
    }

    /**
     * @param Node\Expr\MethodCall $methodCall
     */
    public function addIocContainerAccessor(Node\Expr\MethodCall $methodCall)
    {
        $this->iocContainerAccessors[] = $methodCall;
    }
}
