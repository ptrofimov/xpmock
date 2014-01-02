<?php
namespace Xpmock;

trait TestCaseTrait
{
    /**
     * @param string $className
     *
     * @return MockWriter
     */
    public function mock($className = 'stdClass', array $object = null)
    {
        $mockWriter = new MockWriter($className, $this, $object);

        return $object ? $mockWriter->new() : $mockWriter;
    }

    /**
     * @param string $className
     *
     * @return MockWriter
     */
    public function stub($className, array $object = null)
    {
        return new MockWriter($className, $this, $object, true);
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
