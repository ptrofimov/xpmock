<?php
namespace Stubs;

trait SignatureTrait
{
    public function methodFromTrait()
    {
    }
}

class Signature
{
    use SignatureTrait;

    const CONSTANT = 'CONSTANT';

    public function publicMethod(
        $simpleVar,
        array &$array,
        \stdClass $stdClass,
        $default = null,
        array $defaultArray = [],
        $defaultConstant = self::CONSTANT
    )
    {
    }

    public static function publicStaticMethod()
    {
    }

    final public function finalPublicMethod()
    {
    }

    protected function protectedMethod()
    {
    }

    private function privateMethod()
    {
    }
}
