<?php
namespace Xpmock;

trait TestCaseTrait
{
    public function mock($className)
    {
        $mock = $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();

        $methods = [];
        $class = new \ReflectionClass($className);
        /** @var $method \ReflectionMethod */
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methods[] = "public function {$method->getName()}(\$i){return call_user_func_array([\$this->mock,'{$method->getName()}'],func_get_args());}";
            $methods[] = "public function mock{$method->getName()}(){return call_user_func_array([\$this,'__mock'],array_merge(['{$method->getName()}'],func_get_args()));}";

        }

        $className = $class->getShortName();
        $mockClassName = $className . '_Mock_' . substr(md5(microtime(true)), 0, 4);
        $methods = implode($methods);
        $code = 'namespace ' . $class->getNamespaceName() . ";class $mockClassName extends $className{use \\Xpmock\\MockTrait;$methods}";

        eval($code);

        $mockClassName = $class->getNamespaceName() . '\\' . $mockClassName;
        $myMock = new $mockClassName($mock);

        return $myMock;
    }
}
