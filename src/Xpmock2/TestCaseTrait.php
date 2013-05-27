<?php
namespace Xpmock2;

trait TestCaseTrait
{
    public function mock($className)
    {
        return new MockBuilder($className, $this);
    }
}
