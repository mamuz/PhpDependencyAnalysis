<?php

namespace PhpDA\Entity;

class Method
{
    const TYPE_CONCRET = 0;

    const TYPE_ABSTRACT = 1;

    const TYPE_FINAL = 2;

    /** @var string */
    private $name;

    /** @var Doc */
    private $doc;

    /** @var bool */
    private $isStatic = false;

    /** @var int */
    private $type;

    /** @var Access */
    private $access;

    /** @var Parameter[] */
    private $parameters;

    /** @var GlobalVar[] */
    private $globalVars;

    /** @var IncludeStmt[] */
    private $includeStmts;

    /** @var Stmt[] */
    private $stmts;

    /** @var Stmt[] */
    private $returns;
}
