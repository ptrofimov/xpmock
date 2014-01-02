# xpmock

[![Build Status](https://travis-ci.org/ptrofimov/xpmock.png?branch=master)](https://travis-ci.org/ptrofimov/xpmock)
[![Latest Stable Version](https://poser.pugx.org/ptrofimov/xpmock/v/stable.png)](https://packagist.org/packages/ptrofimov/xpmock)
[![Total Downloads](https://poser.pugx.org/ptrofimov/xpmock/downloads.png)](https://packagist.org/packages/ptrofimov/xpmock)

Introduction [[in English]](http://ptrofimov.github.io/) [[на русском]](http://habrahabr.ru/post/183010/)

**!!! New version 1.1.5: [multiple improvements](https://github.com/ptrofimov/xpmock#new)**

PHPUnit is standard testing framework in PHP world.
No wonder - it is nifty.
But speaking about way of mocking objects in PHPUnit many people complain on a bit redundant syntax.
They suggest many addons for PHPUnit to create mocks like Mockery (I know this is not just addon).

But I am sure PHPUnit has well-developed mocking system.
And this project XPMock is a way to simplify this syntax a bit.
I need to underline that XPMock doesn't create some own mock objects.
XPMock just calls the same PHPUnit methods creating the same native mocks but much simpler.

### Write this
```php
$this->mock('MyClass')
    ->getBool(true)
    ->getNumber(1)
    ->getString('string')
    ->new();
```
### instead of that
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

// init mock writer

$this->mock('MyClass') // init mock (all methods are real by default)

// mock methods

// $mock->expects($this->any())->method('getNumber')->will($this->returnValue(null))
->getNumber()
// $mock->expects($this->any())->method('getNumber')->will($this->returnValue(1))
->getNumber(1)
// $mock::staticExpects($this->any())->method('getNumber')->will($this->returnValue(1))
->getNumber(1)
// $mock->expects($this->any())->method('getNumber')->will($this->returnValue(1))
->getNumber($this->returnValue(1))
// $mock->expects($this->once())->method('getNumber')->will($this->returnValue(null))
->getNumber($this->once())
// $mock->expects($this->once())->method('getNumber')->will($this->returnValue(1))
->getNumber(1, $this->once())
// $mock->expects($this->at(0))->method('getNumber')->will($this->returnValue(1))
// $mock->expects($this->at(1))->method('getNumber')->will($this->returnValue(2))
->getNumber(1, $this->at(0))
->getNumber(2, $this->at(1))
// $mock->expects($this->once())->method('getNumber')->with(1,2,3)->will($this->returnValue(null))
->getNumber([1,2,3], $this->once())
// $mock->expects($this->any())->method('getNumber')->with(1,2,3)->will($this->returnValue(1))
->getNumber([1,2,3], 1)
// $mock->expects($this->once())->method('getNumber')->with(1,2,3)->will($this->returnValue(1))
->getNumber([1,2,3], 1, $this->once())
// $mock->expects($this->any())->method('getNumber')->will($this->returnCallback(function(){}))
->getNumber(function(){})
// $mock->expects($this->any())->method('getNumber')->will($this->throwException(new \Exception('')))
->getNumber(new \Exception(''))

// create mock

// $this->getMockBuilder('MyClass')->disableOriginalConstructor()->getMock()
->new()
// $this->getMockBuilder('MyClass')->setConstructorArgs([1,2,3])->getMock()
->new(1, 2, 3)
```

## Handy reflection methods

```php

// get value of any property: static/non-static, public/protected/private

$value = $this->reflect('MyClass')->property; // class name (only static)
$value = $this->reflect(new MyClass())->property; // object

// set value of any property: static/non-static, public/protected/private property

$this->reflect('MyClass')->property = $value; // class name (only static)
$this->reflect(new MyClass())->property = $value; // object
$this->reflect(new MyClass())
    ->__set('property1', $value1)
    ->__set('property2', $value2); // chain

// call any method: static/non-static, public/protected/private

$this->reflect('MyClass')->method($arg); // class name (only static)
$this->reflect(new MyClass())->method($arg); // object

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
class MyTestCase extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;
}
```
OR Option 2. Extend your test case from xpmock's one:
```php
class MyTestCase extends \Xpmock\TestCase
{
    
}
```

## NEW

1. If you need to create object with some methods and classname doesn't matter, you could easily do it like this:
```php
$mock = $this->mock()
    ->getString('string')
    ->getNumber(function () {
        return 2 + 2;
    })
    ->new();
```

2. Mocking static methods
3. Mocking abstract methods
4. Brief syntax to create mocks
```php
$mock = $this->mock('MyClass', ['getNumber' => 1]);
```

5. Special method this() inside each mock gives you possibility to change
non-public properties and call non-public methods of mock via Xpmock\Reflection
```php
$mock = $this->mock('MyClass')->new();
$mock->this()->protectedProperty = 'value';
$mock->this()->protectedMethod();
```

6. You can use $this pointer inside your fake methods
```php
$mock = $this->mock('MyClass',
    [
        'property' => 1,
        'getProperty' => function () {
            return $this->property;
        },
    ]
);
```

7. It is easy now to create mocks all methods of which return some value, for example null (stub)
```php
$mock = $this->mock('MyClass', null);
```

8. Very expected: now it is possible to adjust mocks after creation with magic method mock()
```php
$myClass = $this->mock('MyClass', null);
$myClass->getNumber(); // null
$myClass->mock()
    ->getNumber(1);
$myClass->getNumber(); // 1
```
