# xpmock

*PHPUnit mocks: simple syntax*

Write this
```php
$this->mock('MyClass')
    ->getBool(true)
    ->getNumber(1)
    ->getString('string')
    ->new();
```
instead of that
```php
$mock = $this->getMockBuilder('MyClass')
    ->setMethods(['getBool', 'getNumber', 'getString'])
    ->disableOriginalConstructor()
    ->getMock();
$mock->expects($this->any())
    ->method('getBool')
    ->will($this->returnValue(true));
$mock->expects($this->any())
    ->method('getNumber')
    ->will($this->returnValue(1));
$mock->expects($this->any())
    ->method('getString')
    ->will($this->returnValue('string'));
```

Tool generates full-functional native PHPUnit mocks.

## Syntax short description

```php
$this->mock('MyClass')
// $mock->expects($this->any())->method('getNumber')->will($this->returnValue(null))
->getNumber()
// $mock->expects($this->any())->method('getNumber')->will($this->returnValue(1))
->getNumber(1)
// $mock->expects($this->any())->method('getNumber')->will($this->returnValue(1))
->getNumber($this->returnValue(1))
// $mock->expects($this->once())->method('getNumber')->will($this->returnValue(null))
->getNumber($this->once())
// $mock->expects($this->once())->method('getNumber')->with(1,2,3)->will($this->returnValue(null))
->getNumber([1,2,3], $this->once())
// $mock->expects($this->any())->method('getNumber')->with(1,2,3)->will($this->returnValue(1))
->getNumber([1,2,3], 1)
// $mock->expects($this->once())->method('getNumber')->with(1,2,3)->will($this->returnValue(1))
->getNumber([1,2,3], 1, $this->once())
// $this->getMockBuilder('MyClass')->disableOriginalConstructor()->getMock()
->new()
// $this->getMockBuilder('MyClass')->setConstructorArgs([1,2,3])->getMock()
->new(1, 2, 3)
```

## Installation

1. If you don't have composer, [install it](http://getcomposer.org)
2. Add xpmock to your project
```
composer require ptrofimov/xpmock:dev-master
```

## Usage

Option 1. Add trait to existing test case:
```php
class MyTestCase extends PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;
}
```
Option 2. Extend your test case from xpmock's one:
```php
class MyTestCase extends \Xpmock\TestCase
{
    
}
```
