<?php
namespace Xpmock;

class ReflectionTest extends \PHPUnit_Framework_TestCase
{
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

    private function reflect($classOrObject)
    {
        return new Reflection($classOrObject);
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
}
