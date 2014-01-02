<?php
namespace Xpmock;

use PHPUnit_Framework_TestCase as PhpUnitTestCase;
use PHPUnit_Framework_MockObject_Stub as Stub;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_Matcher_InvokedRecorder as InvokedRecorder;
use PHPUnit_Framework_MockObject_Matcher_InvokedAtIndex as InvokedAtIndex;

class Base
{
    /** @return array */
    protected function parseMockMethodArgs($method, array $args)
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
        } elseif (!$will instanceof Stub && !$will instanceof \Closure && !is_null($will)) {
            $will = PhpUnitTestCase::returnValue($will);
        }

        return array(
            'method' => $method,
            'expects' => $expects,
            'with' => $with,
            'will' => $will,
        );
    }

    protected function addMethodExpectation(\ReflectionClass $reflection, MockObject $mock, array $expectation)
    {
        if (is_null($expectation['will'])) {
            return;
        }
        $expect = $reflection->hasMethod($expectation['method'])
        && $reflection->getMethod($expectation['method'])->isStatic()
            ? $mock::staticExpects($expectation['expects'])
            : $mock->expects($expectation['expects']);
        $expect->method($expectation['method'])
            ->will(
                $expectation['will'] instanceof \Closure
                    ? PhpUnitTestCase::returnCallback(
                    version_compare(PHP_VERSION, '5.4.0', '>=') ? $expectation['will']->bindTo($mock, $reflection->getName()) : $expectation['will']
                ) : $expectation['will']
            );
        if (!is_null($expectation['with'])) {
            call_user_func_array(array($expect, 'with'), $expectation['with']);
        }
    }
}
