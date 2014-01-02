<?php
namespace Xpmock;

use PHPUnit_Framework_TestCase as PhpUnitTestCase;
use PHPUnit_Framework_MockObject_Stub as Stub;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_Matcher_InvokedRecorder as InvokedRecorder;
use PHPUnit_Framework_MockObject_Matcher_InvokedAtIndex as InvokedAtIndex;

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
    private $items = array();
    /** @var bool */
    private $isStub;
    /** @var array */
    private $properties = array();

    /**
     * @param string $className
     * @param TestCase $testCase
     * @param bool $isStub
     */
    public function __construct(
        $className,
        PhpUnitTestCase $testCase,
        array $object = null,
        $isStub = false
    )
    {
        $this->className = (string) $className;
        $this->testCase = $testCase;
        $this->isStub = $isStub === true;
        if (!is_null($object)) {
            $reflection = new \ReflectionClass($this->className);
            foreach ($object as $key => $value) {
                if ($reflection->hasProperty($key)) {
                    $this->properties[$key] = $value;
                } else {
                    $this->addMethodExpectation($key, array($value));
                }
            }
        }
    }

    /** @return self|MockObject */
    public function __call($method, array $args)
    {
        return $method == 'new'
            ? $this->buildMock($args)
            : $this->addMethodExpectation($method, $args);
    }

    /** @return MockObject */
    private function buildMock(array $args)
    {
        $mockBuilder = $this->testCase->getMockBuilder($this->className);
        if (!$this->isStub) {
            $mockBuilder->setMethods(
                array_merge($this->extractMethods(), array('this'))
            );
        }
        if ($args) {
            $mockBuilder->setConstructorArgs($args);
        } else {
            $mockBuilder->disableOriginalConstructor();
        }
        $mock = $mockBuilder->getMock();
        $reflection = new \ReflectionClass($this->className);
        foreach ($this->items as $item) {
            $expect = $reflection->hasMethod($item['method'])
            && $reflection->getMethod($item['method'])->isStatic()
                ? $mock::staticExpects($item['expects'])
                : $mock->expects($item['expects']);
            $expect->method($item['method'])
                ->will(
                    $item['will'] instanceof \Closure && version_compare(PHP_VERSION, '5.4.0', '>=')
                        ? PhpUnitTestCase::returnCallback($item['will']->bindTo($mock, $this->className))
                        : $item['will']
                );
            if (!is_null($item['with'])) {
                call_user_func_array(array($expect, 'with'), $item['with']);
            }
        }
        $mock->expects(TestCase::any())
            ->method('this')
            ->will(TestCase::returnValue(new Reflection($mock)));
        foreach ($this->properties as $key => $value) {
            $mock->this()->{$key} = $value;
        }

        return $mock;
    }

    /** @return self */
    private function addMethodExpectation($method, array $args)
    {
        $expects = TestCase::any();
        $with = null;
        $will = TestCase::returnValue(null);

        if (count($args) == 1) {
            if ($args[0] instanceof InvokedRecorder || $args[0] instanceof InvokedAtIndex) {
                $expects = $args[0];
            } else {
                $will = $args[0];
            }
        } elseif (count($args) == 2) {
            if ($args[1] instanceof InvokedRecorder || $args[1] instanceof InvokedAtIndex) {
                list($will, $expects) = $args;
            } elseif (is_array($args[0])) {
                list($with, $will) = $args;
            } else {
                throw new \InvalidArgumentException();
            }
        } elseif (count($args) == 3) {
            if (is_array($args[0]) && ($args[2] instanceof InvokedRecorder || $args[2] instanceof InvokedAtIndex)) {
                list($with, $will, $expects) = $args;
            } else {
                throw new \InvalidArgumentException();
            }
        }

        if ($will instanceof \Exception) {
            $will = PhpUnitTestCase::throwException($will);
        } elseif (!$will instanceof Stub && !$will instanceof \Closure) {
            $will = PhpUnitTestCase::returnValue($will);
        }

        $this->items[] = array(
            'method' => $method,
            'expects' => $expects,
            'with' => $with,
            'will' => $will,
        );

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
