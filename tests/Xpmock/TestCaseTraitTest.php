<?php
namespace Xpmock;

require_once(dirname(__DIR__) . '/stubs/My.php');

class TestCaseTraitTest extends \PHPUnit_Framework_TestCase
{
    use TestCaseTrait;

    public function testMock()
    {
        $mock = $this->mock('Stubs\My');

        $this->assertInstanceOf('Stubs\My', $mock);
    }
}
