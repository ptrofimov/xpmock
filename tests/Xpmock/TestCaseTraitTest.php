<?php
namespace Xpmock;

require_once(dirname(__DIR__) . '/stubs/Signature.php');

class TestCaseTraitTest extends \PHPUnit_Framework_TestCase
{
    use TestCaseTrait;

    public function testMock()
    {
        $mock = $this->mock('Stubs\Signature');

        $class = new \ReflectionClass(get_class($mock));

        $this->assertEquals('Stubs', $class->getNamespaceName());
        $this->assertTrue($class->isSubclassOf('Stubs\Signature'));
        $this->assertEquals(['Xpmock\MockTrait'], $class->getTraitNames());

        $methodNames = [];
        foreach ($class->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() == $class->getName()) {
                $methodNames[] = $method->getName();
            }
        }

        $this->assertEquals(
            [
                'publicMethod',
                'mockPublicMethod',
                'methodFromTrait',
                'mockMethodFromTrait',
                '__construct',
                '__mock'
            ],
            $methodNames
        );

        $publicMethod = $class->getMethod('publicMethod');

        $this->assertEquals(6, $publicMethod->getNumberOfParameters());
        $this->assertEquals(3, $publicMethod->getNumberOfRequiredParameters());

        $params = $publicMethod->getParameters();

        $this->assertEquals('simpleVar', $params[0]->getName());
        $this->assertEquals(false, $params[0]->getClass());
        $this->assertEquals(false, $params[0]->isArray());
        $this->assertEquals(false, $params[0]->isPassedByReference());
        $this->assertEquals(false, $params[0]->isOptional());

        $this->assertEquals('array', $params[1]->getName());
        $this->assertEquals(false, $params[1]->getClass());
        $this->assertEquals(true, $params[1]->isArray());
        $this->assertEquals(true, $params[1]->isPassedByReference());
        $this->assertEquals(false, $params[1]->isOptional());

        $this->assertEquals('stdClass', $params[2]->getName());
        $this->assertEquals('stdClass', $params[2]->getClass()->getName());
        $this->assertEquals(false, $params[2]->isArray());
        $this->assertEquals(false, $params[2]->isPassedByReference());
        $this->assertEquals(false, $params[2]->isOptional());

        $this->assertEquals('default', $params[3]->getName());
        $this->assertEquals(false, $params[3]->getClass());
        $this->assertEquals(false, $params[3]->isArray());
        $this->assertEquals(false, $params[3]->isPassedByReference());
        $this->assertEquals(true, $params[3]->isOptional());
        $this->assertEquals(null, $params[3]->getDefaultValue());

        $this->assertEquals('defaultArray', $params[4]->getName());
        $this->assertEquals(false, $params[4]->getClass());
        $this->assertEquals(true, $params[4]->isArray());
        $this->assertEquals(false, $params[4]->isPassedByReference());
        $this->assertEquals(true, $params[4]->isOptional());
        $this->assertEquals([], $params[4]->getDefaultValue());

        $this->assertEquals('defaultConstant', $params[5]->getName());
        $this->assertEquals(false, $params[5]->getClass());
        $this->assertEquals(false, $params[5]->isArray());
        $this->assertEquals(false, $params[5]->isPassedByReference());
        $this->assertEquals(true, $params[5]->isOptional());
        $this->assertEquals('CONSTANT', $params[5]->getDefaultValue());

        $mockProperty = new \ReflectionProperty(get_class($mock), 'mock');
        $mockProperty->setAccessible(true);

        $this->assertInstanceOf('Stubs\Signature', $mockProperty->getValue($mock));
    }
}
