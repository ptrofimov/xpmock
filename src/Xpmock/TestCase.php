<?php
namespace Xpmock;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $className
     *
     * @return MockWriter
     */
    public function mock($className = 'stdClass')
    {
        return new MockWriter($className, $this);
    }

    /**
     * @param string $className
     *
     * @return MockWriter
     */
    public function stub($className)
    {
        return new MockWriter($className, $this, true);
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
