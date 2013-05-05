<?php
namespace Stubs;

class My
{
    public function getNumber($i)
    {
        return $i + 1;
    }
}

class MyMock extends My
{
    use \Xpmock\MockTrait;

    public function getNumber($i)
    {
        return call_user_func_array(
            [$this->mock, 'getNumber'],
            func_get_args()
        );
    }

    public function mockGetNumber()
    {
        return call_user_func_array(
            [$this, '__mock'],
            array_merge(['getNumber'], func_get_args())
        );
    }
}
