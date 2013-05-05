<?php
namespace Xpmock;

trait MockTrait
{
    private static $mock;

    public function __construct()
    {
    }

    private function __mock($method, $value, $invokeCount = null)
    {
        if ($value instanceof \Closure) {
            $value = \PHPUnit_Framework_TestCase::returnCallback($value);
        } elseif (!$value instanceof PHPUnit_Framework_MockObject_Stub) {
            $value = \PHPUnit_Framework_TestCase::returnValue($value);
        }

        if (is_null($invokeCount)) {
            $invokeCount = \PHPUnit_Framework_TestCase::any();
        }

        self::$mock->expects($invokeCount)
            ->method($method)
            ->will($value);

        return $this;
    }
}
