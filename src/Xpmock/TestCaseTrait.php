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
            if ($method->isFinal() || $method->isStatic()) {
                continue;
            }
            $params = [];
            foreach ($method->getParameters() as $param) {
                $p = '$' . $param->getName();
                if ($param->isPassedByReference()) {
                    $p = '&' . $p;
                }
                if ($param->isArray()) {
                    $p = 'array ' . $p;
                }
                if ($param->getClass()) {
                    $p = '\\' . $param->getClass()->getName() . ' ' . $p;
                }
                if ($param->isOptional()) {
                    $p .= '=' . var_export($param->getDefaultValue(), true);
                }
                $params[] = $p;
            }
            $params = implode(',', $params);

            $methods[] = "public function {$method->getName()}($params){return call_user_func_array([self::\$mock,'{$method->getName()}'],func_get_args());}";
            $ucMethod = ucfirst($method->getName());
            $methods[] = "public function mock{$ucMethod}(){return call_user_func_array([\$this,'__mock'],array_merge(['{$method->getName()}'],func_get_args()));}";
        }

        $className = $class->getShortName();
        $mockClassName = $className . '_Mock_' . substr(md5(microtime(true)), 0, 4);
        $methods = implode($methods);
        $code = 'namespace ' . $class->getNamespaceName() . ";class $mockClassName extends $className{use \\Xpmock\\MockTrait;$methods}";

        @eval($code);

        $mockClassName = $class->getNamespaceName() . '\\' . $mockClassName;
        if (!in_array($mockClassName, get_declared_classes())) {
            throw new \RuntimeException("Failed to create mock for class '{$class->getName()}'");
        }
        $myMock = new $mockClassName();

        $mockProperty = new \ReflectionProperty(get_class($myMock), 'mock');
        $mockProperty->setAccessible(true);
        $mockProperty->setValue($mock);

        return $myMock;
    }
}
