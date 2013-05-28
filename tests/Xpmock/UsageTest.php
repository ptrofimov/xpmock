<?php
namespace Xpmock;

require_once(dirname(__DIR__) . '/stubs/Usage.php');

class UsageTest extends \PHPUnit_Framework_TestCase
{
    use TestCaseTrait;

    private function cleanupMock($mock)
    {
        $mock->__phpunit_cleanup();
    }

    public function testReturnValue()
    {
        $mock = $this->mock('Stubs\Usage')
            ->getNumber(1)
            ->new();

        $this->assertEquals(1, $mock->getNumber());
    }

    public function testReturnNativeValue()
    {
        $mock = $this->mock('Stubs\Usage')
            ->getNumber($this->returnValue(1))
            ->new();

        $this->assertEquals(1, $mock->getNumber());
    }

    public function testReturnCallback()
    {
        $mock = $this->mock('Stubs\Usage')
            ->getNumber(function () {
            return 1;
        })->new();

        $this->assertSame(1, $mock->getNumber());
    }

    public function testExpectsOnce()
    {
        $mock = $this->mock('Stubs\Usage')
            ->getNumber($this->once())
            ->new();

        try {
            $mock->getNumber();
            $mock->getNumber();
            $this->fail();
        } catch (\PHPUnit_Framework_ExpectationFailedException $ex) {
        }

        $this->cleanupMock($mock);
    }

    public function testExpectsTwice()
    {
        $mock = $this->mock('Stubs\Usage')
            ->getNumber($this->exactly(2))
            ->new();

        $this->assertNull($mock->getNumber());

        try {
            $this->verifyMockObjects();
            $this->fail();
        } catch (\PHPUnit_Framework_ExpectationFailedException $ex) {
        }

        $this->cleanupMock($mock);
    }

    public function testWillExpects()
    {
        $mock = $this->mock('Stubs\Usage')
            ->getNumber(1, $this->once())
            ->new();

        $this->assertEquals(1, $mock->getNumber());

        try {
            $mock->getNumber();
            $this->fail();
        } catch (\PHPUnit_Framework_ExpectationFailedException $ex) {
        }

        $this->cleanupMock($mock);
    }

    public function testWithWill()
    {
        $mock = $this->mock('Stubs\Usage')
            ->getNumber(array(1, 2, 3), 1)
            ->new();

        $this->assertEquals(1, $mock->getNumber(1, 2, 3));

        try {
            $mock->getNumber();
            $this->fail();
        } catch (\PHPUnit_Framework_ExpectationFailedException $ex) {
        }

        $this->cleanupMock($mock);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithWillInvalid()
    {
        $this->mock('Stubs\Usage')
            ->getNumber('invalid', 1);
    }

    public function testWithWillExpects()
    {
        $mock = $this->mock('Stubs\Usage')
            ->getNumber(array(1, 2, 3), 1, $this->once())
            ->new();

        $this->assertEquals(1, $mock->getNumber(1, 2, 3));

        try {
            $mock->getNumber();
            $this->fail();
        } catch (\PHPUnit_Framework_ExpectationFailedException $ex) {
        }

        try {
            $mock->getNumber(1, 2, 3);
            $this->fail();
        } catch (\PHPUnit_Framework_ExpectationFailedException $ex) {
        }

        $this->cleanupMock($mock);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithWillExpectsInvalid()
    {
        $this->mock('Stubs\Usage')
            ->getNumber('invalid', 1, 1);
    }
}
