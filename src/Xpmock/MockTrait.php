<?php
namespace Xpmock;

trait MockTrait
{
    private $mock;

    public function __construct($mock)
    {
        $this->mock = $mock;
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

        $this->mock->expects($invokeCount)
            ->method($method)
            ->will($value);

        return $this;
    }
}
