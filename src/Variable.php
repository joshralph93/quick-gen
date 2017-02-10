<?php

namespace QuickGen;

/**
 * Class Variable
 *
 * @package QuickGen
 */
class Variable
{
    /**
     * Value
     *
     * @var string
     */
    private $value;


    /**
     * Variable constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Transform to camel case
     *
     * @return static
     */
    public function camel()
    {
        return new static(camel_case($this->value));
    }

    /**
     * Transform to studly case
     *
     * @return static
     */
    public function studly()
    {
        return new static(studly_case($this->value));
    }

    /**
     * Transform to snake case
     *
     * @return static
     */
    public function snake()
    {
        return new static(snake_case($this->value));
    }

    /**
     * Convert string to spaced words
     *
     * @return static
     */
    public function words()
    {
        $value = preg_replace('/[_-]+/', ' ', $this->value);
        $value = preg_replace('/([a-z])([A-Z])/', '$1 $2', $value);
        $value = strtolower($value);

        return new static($value);
    }
    
    /**
     * Convert string to lowercase
     *
     * @return static
     */
    public function lower()
    {
        $value = strtolower($value);

        return new static($value);
    }
    
    /**
     * Upper case first letters
     *
     * @return static
     */
    public function ucwords()
    {
        return new static(ucwords($this->value));
    }

    /**
     * Convert to plural
     *
     * @return static
     */
    public function plural()
    {
        return new static(str_plural($this->value));
    }

    /**
     * Convert to singular
     *
     * @return static
     */
    public function singular()
    {
        return new static(str_singular($this->value));
    }

    /**
     * Get value
     *
     * @return string
     */
    public function value()
    {
        return $this->value;
    }
}
