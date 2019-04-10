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

namespace PhpDA\Writer\Strategy;

use Fhaculty\Graph\Graph;

class Html extends AbstractGraphViz
{
    /** @var string */
    private $imagePlaceholder = '{GRAPH_IMAGE}';

    /** @var string */
    private $template;

    /**
     * @param string $imagePlaceholder
     * @return Html
     */
    public function setImagePlaceholder($imagePlaceholder)
    {
        $this->imagePlaceholder = $imagePlaceholder;
        return $this;
    }

    /**
     * @return string
     */
    public function getImagePlaceholder()
    {
        return $this->imagePlaceholder;
    }

    /**
     * @param string $template
     * @return Html
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        if (!is_string($this->template)) {
            $this->setDefaultTemplate();
        }

        return $this->template;
    }

    private function setDefaultTemplate()
    {
        $this->setTemplate('<html><body>' . $this->getImagePlaceholder() . '</body></html>');
    }

    protected function toString(Graph $graph)
    {
        return str_replace(
            $this->getImagePlaceholder(),
            $this->getGraphViz()->setFormat('svg')->createImageHtml($graph),
            $this->getTemplate()
        );
    }
}
