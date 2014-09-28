<?php

namespace PhpDA\Parser;

use PhpDA\Entity\Script;
use PhpDA\Feature\ParserInterface;
use PhpDA\Mapper\ScriptAwareTrait as ScriptMapperAwareTrait;
use PhpParser\Error;
use PhpParser\Parser;

class Analyzer implements ParserInterface
{
    use ScriptMapperAwareTrait;

    /** @var Parser */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function analyze($code)
    {
        $script = new Script;

        try {
            $stmts = $this->parser->parse($code);
            $this->getScriptMapper()->populate($stmts)->to($script);
        } catch (Error $e) {
            $script->setError($e->getMessage());
        }

        return $script;
    }
}
