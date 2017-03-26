<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module
{
    protected $requiredFields = ['tool'];

    /**
     * @param string $filepath
     * @return array
     */
    public function readGraphNodeTitlesFrom($filepath)
    {
        $xml = simplexml_load_file($filepath);
        $xml->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');
        $titles = array_map('strval', $xml->xpath('/svg:svg/svg:g[1]/svg:g/svg:title'));
        sort($titles);

        return $titles;
    }

    /**
     * @return string
     */
    public function getTool()
    {
        return $this->config['tool'];
    }
}
