<?php
namespace Xpmock;

class ReflectionTest extends \PHPUnit_Framework_TestCase
{
    public $publicProperty = 1;
    public static $publicStaticProperty = 2;
    private $privateProperty = 3;
    private static $privateStaticProperty = 4;

    public function dataProviderTestGetProperty()
    {
        return [
            [__CLASS__, 'publicStaticProperty', self::$publicStaticProperty],
            [__CLASS__, 'privateStaticProperty', self::$privateStaticProperty],
            [$this, 'publicProperty', $this->publicProperty],
            [$this, 'publicStaticProperty', self::$publicStaticProperty],
            [$this, 'privateProperty', $this->privateProperty],
            [$this, 'privateStaticProperty', self::$privateStaticProperty],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetProperty
     */
    public function testGetProperty($classOrObject, $propertyName, $result)
    {
        $this->assertSame($result, (new Reflection($classOrObject))->{$propertyName});
    }
}
