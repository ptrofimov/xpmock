<?php
namespace Pumock;

class TestCaseTraitTest extends \PHPUnit_Framework_TestCase
{
    use TestCaseTrait;

    public function testMock()
    {
        $this->mock('My');
    }
}