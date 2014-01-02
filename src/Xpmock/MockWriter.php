<?php
namespace Xpmock;

use PHPUnit_Framework_TestCase as PhpUnitTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class MockWriter extends Base
{
    /** @var string */
    private $className;
    /** @var TestCase */
    private $testCase;
    /** @var array */
    private $items = array();
    /** @var array */
    private $properties = array();

    /**
     * @param string $className
     * @param TestCase $testCase
     */
    public function __construct($className, PhpUnitTestCase $testCase, $object = array())
    {
        $this->className = (string) $className;
        $this->testCase = $testCase;
        $reflection = new \ReflectionClass($this->className);
        if (!is_array($object)) {
            $methods = array();
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $methods[$method->getName()] = $object;
            }
            $object = $methods;
        }
        foreach ($object as $key => $value) {
            if ($reflection->hasProperty($key)) {
                $this->properties[$key] = $value;
            } else {
                $this->addMethod($key, array($value));
            }
        }
    }

    /** @return self|MockObject */
    public function __call($method, array $args)
    {
        return $method == 'new'
            ? $this->buildMock($args)
            : $this->addMethod($method, $args);
    }

    /** @return MockObject */
    private function buildMock(array $args)
    {
        $mockBuilder = $this->testCase->getMockBuilder($this->className);
        $mockBuilder->setMethods(
            array_merge($this->extractMethods(), array('this', 'mock'))
        );
        if ($args) {
            $mockBuilder->setConstructorArgs($args);
        } else {
            $mockBuilder->disableOriginalConstructor();
        }
        $mock = $mockBuilder->getMock();
        $reflection = new \ReflectionClass($this->className);
        foreach ($this->items as $item) {
            $this->addMethodExpectation($reflection, $mock, $item);
        }
        $mock->expects(TestCase::any())
            ->method('this')
            ->will(TestCase::returnValue(new Reflection($mock)));
        $mock->expects(TestCase::any())
            ->method('mock')
            ->will(TestCase::returnValue(new MockAdjuster($mock, $reflection)));
        foreach ($this->properties as $key => $value) {
            $mock->this()->{$key} = $value;
        }

        return $mock;
    }

    /** @return self */
    private function addMethod($method, array $args)
    {
        $this->items[] = $this->parseMockMethodArgs($method, $args);

        return $this;
    }

    /**
     * Extract methods from items
     *
     * @return array Unique method names
     */
    private function extractMethods()
    {
        $methods = array();
        foreach ($this->items as $item) {
            $methods[$item['method']] = true;
        }

        return array_keys($methods);
    }
}
