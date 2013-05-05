<?php
require_once(__DIR__ . '/vendor/autoload.php');

class MyTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

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
 * + add composer
 * add tests
 * this for callbacks
 * with parameters restrictions
 * mock for non existent classes
 * mocks for objects
 * mocks for stdclass
 * mocks for static methods
 * mocks for abstract classes
 * mocks for traits
 */
