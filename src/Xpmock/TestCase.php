<?php
namespace Xpmock;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $className
     * @param mixed $object
     *
     * @return MockWriter
     */
    public function mock($className = 'stdClass', $object = array())
    {
        $mockWriter = new MockWriter($className, $this, $object);

        return $object !== array() ? $mockWriter->new() : $mockWriter;
    }

    /**
     * @param string|object $classOrObject
     *
     * @return Reflection
     */
    public function reflect($classOrObject)
    {
        return new Reflection($classOrObject);
    }
}
