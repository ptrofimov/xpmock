<?php
namespace Xpmock;

trait TestCaseTrait
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
