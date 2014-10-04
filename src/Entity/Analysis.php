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

    /** @var Node/Name */
    private $declaredNamespace;

    /** @var Node/Name */
    private $usedNamespaces = array();

    public function __construct()
    {
        $this->declaredNamespace = new Node\Name('\\');
    }

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
     * @param Node\Name $name
     * @return void
     */
    public function setDeclaredNamespace(Node\Name $name)
    {
        $this->declaredNamespace = $name;
    }

    /**
     * @return Node\Name
     */
    public function getDeclaredNamespace()
    {
        return $this->declaredNamespace;
    }

    /**
     * @param Node\Name $usedNamespace
     */
    public function addUsedNamespace(Node\Name $usedNamespace)
    {
        $this->usedNamespaces[] = $usedNamespace;
    }

    /**
     * @return Node\Name[]
     */
    public function getUsedNamespaces()
    {
        return $this->usedNamespaces;
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
