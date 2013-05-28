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

Trait generates full-functional native PHPUnit mocks.
