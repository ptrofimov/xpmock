<?php
namespace Xpmock;

require_once(dirname(__DIR__) . '/stubs/Usage.php');

class UsageTest extends \PHPUnit_Framework_TestCase
{
    use TestCaseTrait;

    private function cleanupMock($mock)
    {
        $mockProperty = new \ReflectionProperty(get_class($mock), 'mock');
        $mockProperty->setAccessible(true);
        $mockProperty->getValue()->__phpunit_cleanup();
    }

    public function testReturnValue()
    {
        $mock = $this->mock('Stubs\Usage');

        $this->assertNull($mock->getNumber());

        $mock->mockGetNumber(1);

        $this->assertEquals(1, $mock->getNumber());
    }

    public function testReturnNativeValue()
    {
        $mock = $this->mock('Stubs\Usage');

        $this->assertNull($mock->getNumber());

        $mock->mockGetNumber($this->returnValue(1));

        $this->assertEquals(1, $mock->getNumber());
    }

    public function testReturnCallback()
    {
        $mock = $this->mock('Stubs\Usage');

        $this->assertNull($mock->getNumber());

        $mock->mockGetNumber(function () {
            return $this;
        });

        $this->assertInstanceOf('Stubs\Usage', $mock->getNumber());
    }

    public function testExpectsOnce()
    {
        $mock = $this->mock('Stubs\Usage');

        $mock->mockGetNumber($this->once());

        $this->assertNull($mock->getNumber());

        try {
            $mock->getNumber();
            $this->fail();
        } catch (\PHPUnit_Framework_ExpectationFailedException $ex) {
        }

        $this->cleanupMock($mock);
    }

    public function testExpectsTwice()
    {
        $mock = $this->mock('Stubs\Usage');

        $mock->mockGetNumber($this->exactly(2));

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
        $mock = $this->mock('Stubs\Usage');

        $mock->mockGetNumber(1, $this->once());

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
        $mock = $this->mock('Stubs\Usage');

        $mock->mockGetNumber([1, 2, 3], 1);

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
        $mock = $this->mock('Stubs\Usage');

        $mock->mockGetNumber('invalid', 1);
    }

    public function testWithWillExpects()
    {
        $mock = $this->mock('Stubs\Usage');

        $mock->mockGetNumber([1, 2, 3], 1, $this->once());

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
        $mock = $this->mock('Stubs\Usage');

        $mock->mockGetNumber('invalid', 1, 1);
    }
}
