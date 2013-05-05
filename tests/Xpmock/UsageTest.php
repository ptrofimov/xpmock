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

    public function testReturn()
    {
        $mock = $this->mock('Stubs\Usage');

        $this->assertNull($mock->getNumber());

        $mock->mockGetNumber(1);

        $this->assertEquals(1, $mock->getNumber());
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
}
