<?php
namespace Xpmock;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $className
     *
     * @return MockWriter
     */
    public function mock($className)
    {
        return new MockWriter($className, $this);
    }
}
