<?php

namespace QuickGen\Compilers;

use QuickGen\Variable;

/**
 * Class StubCompiler
 *
 * @package QuickGen\Compilers
 */
class StubCompiler
{
    /**
     * Path to file to be compiled
     *
     * @var string
     */
    private $file;

    /**
     * Variables array
     *
     * @var array
     */
    private $variables;

    /**
     * Configuration array
     *
     * @var array
     */
    private $config;


    /**
     * StubCompiler constructor.
     *
     * @param $file
     * @param array $variables
     * @param array $config
     */
    public function __construct($file, array $variables, array $config)
    {
        $this->file = $file;
        $this->variables = $variables;
        $this->config = $config;
    }

    /**
     * Compile file contents
     *
     * @return string
     */
    public function compileContent()
    {
        $fullPath = rtrim($this->config['stub_path'], '/') . '/' . $this->file;

        return $this->compile(file_get_contents($fullPath));
    }

    /**
     * Compile file name
     *
     * @return string
     */
    public function compileFilename()
    {
        return $this->compile(str_replace('.stub', '', $this->file), ['__', '__']);
    }

    /**
     * Compile string
     *
     * @param $string
     * @param array $delimiters
     *
     * @return string
     */
    protected function compile($string, $delimiters = ['<<', '>>'])
    {
        return preg_replace_callback('/' . implode('(.*?)', $delimiters) . '/', function ($parts) {

            // Find the value that a match should be replaced with.
            // We'll also run the variable through the filters to transform it to the correct format.
            $replacement = $this->filterVariable(
                $this->getReplacementOptions($parts[1])
            );

            return $replacement->value();
        }, $string);
    }

    /**
     * Get replacement options for a matched part
     *
     * @param $replacement
     *
     * @return array
     */
    private function getReplacementOptions($replacement)
    {
        $parts    = explode('.', $replacement);
        $variable = array_shift($parts);
        $variable = snake_case($variable);
        $filters  = $parts;

        return compact('variable', 'filters');
    }

    /**
     * Find a variable and pass run the given filters on it
     *
     * @param array $options
     *
     * @return Variable
     */
    private function filterVariable(array $options)
    {
        /** @var Variable $variable */
        $variable = $this->variables[$options['variable']];

        foreach ($options['filters'] as $filter) {
            $variable = $variable->{$filter}();
        }

        return $variable;
    }
}