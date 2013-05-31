<?php
namespace Xpmock;

class Reflection
{
    /** @var string */
    private $class;
    /** @var object|null */
    private $object;

    public function __construct($classOrObject)
    {
        list($this->class, $this->object) = is_object($classOrObject)
            ? array(get_class($classOrObject), $classOrObject)
            : array((string) $classOrObject, null);
    }

    public function __get($key)
    {
        $property = new \ReflectionProperty($this->class, $key);
        if (!$property->isPublic()) {
            $property->setAccessible(true);
        }

        return $property->isStatic()
            ? $property->getValue()
            : $property->getValue($this->object);
    }
}