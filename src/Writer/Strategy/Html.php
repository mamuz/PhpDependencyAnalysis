<?php

namespace PhpDA\Writer\Strategy;

class Html extends AbstractStrategy
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

    /**
     * @return void
     */
    private function setDefaultTemplate()
    {
        $this->setTemplate('<html><body>' . $this->getImagePlaceholder() . '</body></html>');
    }

    public function createOutput()
    {
        $this->getGraphViz()->setFormat('svg');

        return str_replace(
            $this->getImagePlaceholder(),
            $this->getGraphViz()->createImageHtml(),
            $this->getTemplate()
        );
    }
}
