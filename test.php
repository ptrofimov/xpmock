<?php

class My
{
    public function getNumber($i)
    {
        return $i + 1;
    }
}

trait MockTrait
{
    private $mock;

    public function __construct($mock)
    {
        $this->mock = $mock;
    }

    private function mockMethod($method, $value, $invokeCount = null)
    {
        if ($value instanceof Closure) {
            $value = PHPUnit_Framework_TestCase::returnCallback($value);
        } elseif (!$value instanceof PHPUnit_Framework_MockObject_Stub) {
            $value = PHPUnit_Framework_TestCase::returnValue($value);
        }

        if (is_null($invokeCount)) {
            $invokeCount = PHPUnit_Framework_TestCase::any();
        }

        $this->mock->expects($invokeCount)
            ->method($method)
            ->will($value);

        return $this;
    }
}

class MyMock extends My
{
    use MockTrait;

    public function getNumber($i)
    {
        return call_user_func_array([$this->mock, 'getNumber'], func_get_args());
    }

    public function mockGetNumber($value, $invokeCount = null)
    {
        return $this->mockMethod('getNumber', $value, $invokeCount);
    }
}

trait TestTrait
{
    public function mock($className)
    {
        $mock = $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();

        $mockClassName = $className . 'Mock';
        $myMock = new $mockClassName($mock);

        return $myMock;
    }
}

class MyTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testGetNumber()
    {
        $my = $this->mock('My')
            ->mockGetNumber(1, $this->once());

        $this->assertEquals(1, $my->getNumber(1));
    }
}


/*
 * TODO
 *
 * + mocks for stubs
 * + invoke count restrictions
 * + callbacks as values
 * this for callbacks
 * with parameters restrictions
 * mock for non existent classes
 * mocks for objects
 * mocks for stdclass
 * mocks for static methods
 * mocks for abstract classes
 * mocks for traits
 */