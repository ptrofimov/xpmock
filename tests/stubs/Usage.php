<?php
namespace Stubs;

class Usage
{
    protected $property = 'real property';

    public function __construct($value)
    {
        throw new \RuntimeException("Value $value from constructor");
    }

    public function getNumber()
    {
        return 1;
    }

    public static function getString()
    {
        return 'real string';
    }

    public function getProperty()
    {
        return $this->property;
    }
}
