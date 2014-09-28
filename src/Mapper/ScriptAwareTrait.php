<?php

namespace PhpDA\Mapper;

use PhpDA\Feature\ScriptMapperInterface;

trait ScriptAwareTrait
{
    /** @var Script */
    private $scriptMapper;

    /**
     * @param ScriptMapperInterface $scriptMapper
     * @return void
     */
    public function setScriptMapper(ScriptMapperInterface $scriptMapper)
    {
        $this->scriptMapper = $scriptMapper;
    }

    /**
     * @return ScriptMapperInterface
     */
    public function getScriptMapper()
    {
        if (!$this->scriptMapper instanceof ScriptMapperInterface) {
            $this->setScriptMapper(new Script);
        }

        return $this->scriptMapper;
    }
}
