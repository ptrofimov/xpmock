<?php
namespace Xpmock;

require_once(dirname(__DIR__) . '/stubs/Usage.php');

class TestCaseTraitTest extends TestCase
{
    private function getProperty($classOrObject, $property)
    {
        $property = new \ReflectionProperty(
            is_object($classOrObject) ? get_class($classOrObject) : $classOrObject,
            $property
        );
        if (!$property->isPublic()) {
            $property->setAccessible(true);
        }
        return $property->isStatic()
            ? $property->getValue($classOrObject)
            : $property->getValue();
    }

    public function testMock()
    {
        $mockWriter = $this->mock('Stubs\Usage');

        $this->assertInstanceOf('Xpmock\MockWriter', $mockWriter);
    }
}
