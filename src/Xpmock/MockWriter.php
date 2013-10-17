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

    /**
     * @param string $className
     * @param TestCase $testCase
     * @param bool $isStub
     */
    public function __construct($className, PhpUnitTestCase $testCase, $isStub = false)
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
                $mockBuilder->setMethods($this->extractMethods());
            }
            if ($args) {
                $mockBuilder->setConstructorArgs($args);
            } else {
                $mockBuilder->disableOriginalConstructor();
            }
            $mock = $mockBuilder->getMock();
            foreach ($this->items as $item) {
                $expect = $mock->expects($item['expects'])
                    ->method($item['method'])
                    ->will($item['will']);
                if (!is_null($item['with'])) {
                    call_user_func_array(array($expect, 'with'), $item['with']);
                }
            }

            return $mock;
        }

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

        if ($will instanceof \Closure) {
            $will = PhpUnitTestCase::returnCallback($will);
        } elseif ($will instanceof \Exception) {
            $will = PhpUnitTestCase::throwException($will);
        } elseif (!$will instanceof Stub) {
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
