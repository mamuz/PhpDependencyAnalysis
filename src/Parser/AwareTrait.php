<?php

namespace PhpDA\Parser;

use PhpDA\Feature\ParserInterface;
use PhpParser\Lexer\Emulative;
use PhpParser\Parser;

trait AwareTrait
{
    /** @var ParserInterface */
    private $parser;

    /**
     * @param ParserInterface $parser
     * @return void
     */
    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return ParserInterface
     */
    public function getParser()
    {
        if (!$this->parser instanceof ParserInterface) {
            $this->setParser($this->createParser());
        }

        return $this->parser;
    }

    /**
     * @return Analyzer
     */
    private function createParser()
    {
        $parser = new Parser(new Emulative);
        return new Analyzer($parser);
    }
}
