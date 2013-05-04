<?php
namespace Pumock;

trait TestCaseTrait
{
    public function mock($className)
    {
        $mock = $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();

        $mockClassName = $className . 'Mock';
        $myMock = new $mockClassName($mock);

        return $myMock;
    }
}
