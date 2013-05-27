<?php
namespace Xpmock2;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_Stub as Stub;
use PHPUnit_Framework_MockObject_Matcher_InvokedRecorder as InvokedRecorder;

class MockBuilder
{
    private $className;
    private $that;
    private $methods = [];

    public function __construct($className, $that)
    {
        $this->className = (string) $className;
        $this->that = $that;
    }

    public function __call($method, array $args)
    {
        if ($method == 'new') {
            $mock = $this->that->getMockBuilder($this->className)
                ->setMethods(array_keys($this->methods));
            if ($args) {
                $mock->setConstructorArgs($args);
            } else {
                $mock->disableOriginalConstructor();
            }
            $mock = $mock->getMock();
            foreach ($this->methods as $method => $item) {
                $expect = $mock->expects($item['expects'])
                    ->method($method)
                    ->will($item['will']);
                if (!is_null($item['with'])) {
                    call_user_func_array([$expect, 'with'], $item['with']);
                }
            }
            return $mock;
        }

        $expects = TestCase::any();
        $with = null;
        $will = TestCase::returnValue(null);

        if (count($args) == 0) {
            throw new \InvalidArgumentException();
        }

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
            $will = TestCase::returnCallback($will /*->bindTo(self::$mock)*/);
        } elseif (!$will instanceof Stub) {
            $will = TestCase::returnValue($will);
        }

        $this->methods[$method] = [
            'expects' => $expects,
            'with' => $with,
            'will' => $will,
        ];

        return $this;
    }
}
