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

    /** @return \Stubs\Usage */
    private function mockUsage()
    {
        return $this->mock('Stubs\Usage');
    }

    public function testMock()
    {
        $mock = $this->mockUsage();

        $this->assertInstanceOf('Xpmock\MockWriter', $mock);
    }

    public function testReturnValue()
    {
        $mock = $this->mockUsage()
            ->getNumber(123)
            ->new();

        $this->assertEquals(123, $mock->getNumber());
    }

    public function testReturnNativeValue()
    {
        $mock = $this->mockUsage()
            ->getNumber($this->returnValue(112))
            ->new();

        $this->assertEquals(112, $mock->getNumber());
    }

    public function testReturnCallback()
    {
        $mock = $this->mockUsage()
            ->getNumber(
            function () {
                return 156;
            })
            ->new();

        $this->assertSame(156, $mock->getNumber());
    }

    /**
     * @expectedException \LogicException
     */
    public function testReturnThrowException()
    {
        $mock = $this->mockUsage()
            ->getNumber(new \LogicException())
            ->new();

        $mock->getNumber();
    }

    public function testExpectsOnce()
    {
        $mock = $this->mockUsage()
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
        $mock = $this->mockUsage()
            ->getNumber($this->exactly(2))
            ->new();

        try {
            $mock->getNumber();
            $this->verifyMockObjects();
            $this->fail();
        } catch (\PHPUnit_Framework_ExpectationFailedException $ex) {
        }

        $this->cleanupMock($mock);
    }

    public function testWillExpects()
    {
        $mock = $this->mockUsage()
            ->getNumber(142, $this->once())
            ->new();

        $this->assertEquals(142, $mock->getNumber());

        try {
            $mock->getNumber();
            $this->fail();
        } catch (\PHPUnit_Framework_ExpectationFailedException $ex) {
        }

        $this->cleanupMock($mock);
    }

    public function testWithWill()
    {
        $mock = $this->mockUsage()
            ->getNumber(array(1, 2, 3), 152)
            ->new();

        $this->assertEquals(152, $mock->getNumber(1, 2, 3));

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
        $this->mockUsage()
            ->getNumber('not_array', 1);
    }

    public function testWithWillExpects()
    {
        $mock = $this->mockUsage()
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
        $this->mockUsage()
            ->getNumber('not_array', 1, 1);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Value 900 from constructor
     */
    public function testNew()
    {
        $this->mockUsage()
            ->new(900);
    }
}
