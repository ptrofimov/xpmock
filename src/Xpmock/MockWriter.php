<?php
namespace Xpmock;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_Stub as Stub;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_Matcher_InvokedRecorder as InvokedRecorder;

/**
 * Class to help with mock-writing
 */
class MockWriter
{
    /** @var string */
    private $className;
    /** @var TestCase */
    private $testCase;
    /** @var array */
    private $methods = array();
    /** @var bool */
    private $isStub;
    /** @var array */
    private $properties = array();
    /** @var array */
    private $injectTo = array();

    /**
     * @param string $className
     * @param TestCase $testCase
     * @param bool $isStub
     */
    public function __construct($className, TestCase $testCase, $isStub = false)
    {
        $this->className = (string) $className;
        $this->testCase = $testCase;
        $this->isStub = $isStub === true;
    }

    /** @return self|MockObject */
    public function __call($method, array $args)
    {
        if ($method == 'new') {
            $mockBuilder = $this->testCase->getMockBuilder($this->className);
            if (!$this->isStub) {
                $mockBuilder->setMethods(array_keys($this->methods));
            }
            if ($args) {
                $mockBuilder->setConstructorArgs($args);
            } else {
                $mockBuilder->disableOriginalConstructor();
            }
            $mock = $mockBuilder->getMock();
            foreach ($this->methods as $method => $item) {
                $expect = $mock->expects($item['expects'])
                    ->method($method)
                    ->will($item['will']);
                if (!is_null($item['with'])) {
                    call_user_func_array([$expect, 'with'], $item['with']);
                }
            }
            foreach ($this->properties as $key => $value) {
                $property = new \ReflectionProperty(get_class($mock), $key);
                $property->setAccessible(true);
                if ($property->isStatic()) {
                    $property->setValue($value);
                } else {
                    $property->setValue($mock, $value);
                }
            }
            foreach ($this->injectTo as $target) {
                $property = new \ReflectionProperty(get_class($target[0]), $target[1]);
                $property->setAccessible(true);
                if ($property->isStatic()) {
                    $property->setValue($mock);
                } else {
                    $property->setValue($target[0], $mock);
                }
            }

            return $mock;
        }

        $expects = TestCase::any();
        $with = null;
        $will = TestCase::returnValue(null);

        if (count($args) == 1) {
            if ($args[0] instanceof InvokedRecorder) {
                $expects = $args[0];
            } else {
                $will = $args[0];
            }
        } elseif (count($args) == 2) {
            if ($args[1] instanceof InvokedRecorder) {
                list($will, $expects) = $args;
            } elseif (is_array($args[0])) {
                list($with, $will) = $args;
            } else {
                throw new \InvalidArgumentException();
            }
        } elseif (count($args) == 3) {
            if (is_array($args[0]) && $args[2] instanceof InvokedRecorder) {
                list($with, $will, $expects) = $args;
            } else {
                throw new \InvalidArgumentException();
            }
        }

        if ($will instanceof \Closure) {
            $will = TestCase::returnCallback($will);
        } elseif ($will instanceof \Exception) {
            $will = TestCase::throwException($will);
        } elseif (!$will instanceof Stub) {
            $will = TestCase::returnValue($will);
        }

        $this->methods[$method] = array(
            'expects' => $expects,
            'with' => $with,
            'will' => $will,
        );

        return $this;
    }

    /** @return self */
    public function __set($key, $value)
    {
        $this->properties[$key] = $value;

        return $this;
    }

    /** @return self */
    public function injectTo($object, $property)
    {
        $this->injectTo[] = [$object, $property];

        return $this;
    }
}
