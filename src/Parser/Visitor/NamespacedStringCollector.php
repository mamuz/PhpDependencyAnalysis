<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class NamespacedStringCollector extends AbstractVisitor
{
    const VALID_CLASSNAME_PATTERN = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Scalar\String) {
            if ($this->match($node)) {
                $this->collect($node);
            }
        }
    }

    /**
     * @param Node\Scalar\String $string
     * @return bool
     */
    private function match(Node\Scalar\String $string)
    {
        $string = $string->value;

        if (!preg_match(self::VALID_CLASSNAME_PATTERN, str_replace('\\', '', $string))) {
            return false;
        }

        if ($this->matchToPsrStandard($string)) {
            return true;
        }

        if ($this->matchToPearStandard($string)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $string
     * @return bool
     */
    private function matchToPsrStandard($string)
    {
        $normalized = ltrim($string, '\\');

        if ($string[0] == '\\' && preg_match(self::VALID_CLASSNAME_PATTERN, $normalized)) {
            return true;
        }

        return $this->matchNamespacesBy('\\', ltrim($normalized, '\\'));
    }

    /**
     * @param string $string
     * @return bool
     */
    private function matchToPearStandard($string)
    {
        return $this->matchNamespacesBy('_', $string);
    }

    /**
     * @param string $glue
     * @param string $string
     * @return bool
     */
    private function matchNamespacesBy($glue, $string)
    {
        $namespaces = explode($glue, $string);

        if (count($namespaces) < 2) {
            return false;
        }

        foreach ($namespaces as $namespace) {
            if (!preg_match(self::VALID_CLASSNAME_PATTERN, $namespace)) {
                return false;
            }
        }

        return true;
    }
}
