<?php
require_once(__DIR__ . '/vendor/autoload.php');

class My
{
    public function getNumber($i)
    {
        return $i + 1;
    }
}

class MyMock extends My
{
    use \Pumock\MockTrait;

    public function getNumber($i)
    {
        return call_user_func_array(
            [$this->mock, 'getNumber'],
            func_get_args()
        );
    }

    public function mockGetNumber()
    {
        return call_user_func_array(
            [$this, '__mock'],
            array_merge(['getNumber'], func_get_args())
        );
    }
}

class MyTest extends \PHPUnit_Framework_TestCase
{
    use \Pumock\TestCaseTrait;

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
