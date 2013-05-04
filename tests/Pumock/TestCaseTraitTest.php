<?php
namespace Pumock;

require_once(dirname(__DIR__) . '/stubs/My.php');

class TestCaseTraitTest extends \PHPUnit_Framework_TestCase
{
    use TestCaseTrait;

    public function testMock()
    {
        $this->mock('Stubs\My');
    }
}