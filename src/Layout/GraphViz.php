<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2019 Marco Muths
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpDA\Layout;

use Graphp\GraphViz\GraphViz as BaseGraphViz;

class GraphViz extends BaseGraphViz
{
    /** @var array */
    private static $groups = [];

    /** @var array */
    private static $groupLayout = [];

    /**
     * @param array $groups
     */
    public function setGroups(array $groups)
    {
        self::$groups = $groups;
    }

    /**
     * @param array $layout
     */
    public function setGroupLayout(array $layout)
    {
        self::$groupLayout = $layout;
    }

    public static function escape($id)
    {
        if (is_int($id) && array_key_exists($id, self::$groups)) {
            $id = self::$groups[$id];
            $id = parent::escape($id);
            return $id . self::getGroupLayoutScript();
        }

        return parent::escape($id);
    }

    /**
     * @return string
     */
    private static function getGroupLayoutScript()
    {
        $script = '';
        foreach (self::$groupLayout as $attr => $val) {
            $script .= self::EOL . $attr . '=' . parent::escape($val) . ';';
        }

        return $script;
    }
}
