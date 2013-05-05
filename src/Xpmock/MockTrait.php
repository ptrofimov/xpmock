<?php
namespace Xpmock;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_Stub as Stub;
use PHPUnit_Framework_MockObject_Matcher_InvokedRecorder as InvokedRecorder;

trait MockTrait
{
    private static $mock;

    public function __construct()
    {
    }

    private function __mock()
    {
        $expects = TestCase::any();
        $with = null;
        $will = TestCase::returnValue(null);

        $args = func_get_args();

        if (count($args) == 0) {
            throw new \InvalidArgumentException();
        }

        $method = array_shift($args);

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
            $will = TestCase::returnCallback($will->bindTo(self::$mock));
        } elseif (!$will instanceof Stub) {
            $will = TestCase::returnValue($will);
        }

        $expect = self::$mock->expects($expects)
            ->method($method)
            ->will($will);
        if (!is_null($with)) {
            call_user_func_array([$expect, 'with'], $with);
        }

        return $this;
    }
}
