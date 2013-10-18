<?php
namespace Stubs;

class Usage
{
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
}
