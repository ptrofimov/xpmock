<?php
namespace Xpmock;

class ReflectionTest extends \PHPUnit_Framework_TestCase
{
    use TestCaseTrait;

    public $publicProperty;
    public static $publicStaticProperty;
    private $privateProperty;
    private static $privateStaticProperty;

    protected function setUp()
    {
        parent::setUp();

        $this->publicProperty = 1;
        self::$publicStaticProperty = 2;
        $this->privateProperty = 3;
        self::$privateStaticProperty = 4;
    }

    public function testGetProperty()
    {
        $this->assertSame(
            self::$publicStaticProperty,
            $this->reflect(__CLASS__)->publicStaticProperty
        );
        $this->assertSame(
            self::$privateStaticProperty,
            $this->reflect(__CLASS__)->privateStaticProperty
        );
        $this->assertSame(
            $this->publicProperty,
            $this->reflect($this)->publicProperty
        );
        $this->assertSame(
            self::$publicStaticProperty,
            $this->reflect($this)->publicStaticProperty
        );
        $this->assertSame(
            $this->privateProperty,
            $this->reflect($this)->privateProperty
        );
        $this->assertSame(
            self::$privateStaticProperty,
            $this->reflect($this)->privateStaticProperty
        );
    }

    public function testSetProperty()
    {
        $this->reflect(__CLASS__)->publicStaticProperty = 5;
        $this->assertSame(5, self::$publicStaticProperty);
        $this->reflect(__CLASS__)->privateStaticProperty = 5;
        $this->assertSame(5, self::$privateStaticProperty);
        $this->reflect($this)->publicProperty = 5;
        $this->assertSame(5, $this->publicProperty);
        $this->reflect($this)->publicStaticProperty = 5;
        $this->assertSame(5, self::$publicStaticProperty);
        $this->reflect($this)->privateProperty = 5;
        $this->assertSame(5, $this->privateProperty);
        $this->reflect($this)->privateStaticProperty = 5;
        $this->assertSame(5, self::$privateStaticProperty);
    }

    public function publicMethod($number)
    {
        return $number + 1;
    }

    private function privateMethod($number)
    {
        return $number + 2;
    }

    public static function publicStaticMethod($number)
    {
        return $number + 3;
    }

    private static function privateStaticMethod($number)
    {
        return $number + 4;
    }

    public function testCallMethod()
    {
        $this->assertSame(4, $this->reflect(__CLASS__)->publicStaticMethod(1));
        $this->assertSame(5, $this->reflect(__CLASS__)->privateStaticMethod(1));
        $this->assertSame(2, $this->reflect($this)->publicMethod(1));
        $this->assertSame(3, $this->reflect($this)->privateMethod(1));
        $this->assertSame(4, $this->reflect($this)->publicStaticMethod(1));
        $this->assertSame(5, $this->reflect($this)->privateStaticMethod(1));
    }
}
