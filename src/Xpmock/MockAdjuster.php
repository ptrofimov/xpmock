<?php
namespace Xpmock;

use PHPUnit_Framework_MockObject_MockObject as MockObject;

class MockAdjuster extends Base
{
    /** @var MockObject */
    private $mock;
    /** @var \ReflectionClass */
    private $reflection;

    /**
     * @param MockObject $mock
     */
    public function __construct(MockObject $mock, \ReflectionClass $reflection)
    {
        $this->mock = $mock;
        $this->reflection = $reflection;
    }

    /** @return self */
    public function __call($method, array $args)
    {
        $this->addMethodExpectation($this->reflection, $this->mock, $this->parseMockMethodArgs($method, $args));

        return $this;
    }
}
