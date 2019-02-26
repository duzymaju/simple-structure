<?php

use PHPUnit\Framework\TestCase;
use SimpleStructure\Container;
use SimpleStructure\Exception\BadDefinitionCallException;

final class ContainerTest extends TestCase
{
    /**
     * Test not existed param getting
     */
    public function testNotExistedParamGetting()
    {
        $container = new Container();
        $this->expectException(BadDefinitionCallException::class);
        $this->expectExceptionMessage('Definition "param" doesn\'t exist.');
        $container->get('param');
    }

    /**
     * Test param setting
     */
    public function testParamSetting()
    {
        $container = new Container();
        $container->setParam('param', 'value');
        $this->assertEquals('value', $container->get('param'));
    }

    /**
     * Test param which overwrites object
     */
    public function testParamWhichOverwritesObject()
    {
        $container = new Container();
        $container->setObject('param', 'ContainerTestClass');
        $container->setParam('param', 'value');
        $this->assertEquals('value', $container->get('param'));
    }

    /**
     * Test object which doesnt overwrite param
     */
    public function testObjectWhichDoesntOverwritesParam()
    {
        $container = new Container();
        $container->setParam('param', 'value');
        $container->setObject('param', 'ContainerTestClass');
        $this->assertEquals('value', $container->get('param'));
    }

    /**
     * Test param which overwrites object even after init
     */
    public function testParamWhichOverwritesObjectEvenAfterInit()
    {
        $container = new Container();
        $container->setObject('param', 'ContainerTestClass');
        $this->assertInstanceOf('ContainerTestClass', $container->get('param'));
        $container->setParam('param', 'value');
        $this->assertEquals('value', $container->get('param'));
    }

    /**
     * Test object setting via "set" alias
     */
    public function testObjectSettingViaSetAlias()
    {
        $container = new Container();
        $container->set('object', 'ContainerTestClass');
        $this->assertInstanceOf('ContainerTestClass', $container->get('object'));
    }

    /**
     * Test object setting with dependencies and params
     */
    public function testObjectSettingWithDependenciesAndParams()
    {
        $container = new Container();
        $container->setParam('param', 'value');
        $container->setObject('object', 'ContainerTestClass', [ 'param' ], [ 123, 456 ]);
        $this->assertEquals([ 'value', 123, 456 ], $container->get('object')->params);
    }

    /**
     * Test object setting with new instance of dependency
     */
    public function testObjectSettingWithNewInstanceOfDependency()
    {
        $container = new Container();
        $container->setObject('object1', 'ContainerTestClass', [], [ 10 ]);
        $container->setObject('object2', 'ContainerTestClass', [ 'i:object1' ]);
        $this->assertEquals(11, $container->get('object2')->params[0]->iterate(0));
        $this->assertEquals(12, $container->get('object2')->params[0]->iterate(0));
        $this->assertEquals(13, $container->get('object2')->params[0]->iterate(0));
        $this->assertEquals(11, $container->create('object2')->params[0]->iterate(0));
        $this->assertEquals(14, $container->get('object2')->params[0]->iterate(0));
        $this->assertEquals(11, $container->create('object2')->params[0]->iterate(0));
    }

    /**
     * Test object setting with definition of dependency
     */
    public function testObjectSettingWithDefinitionOfDependency()
    {
        $container = new Container();
        $container->setObject('object1', 'ContainerTestClass');
        $container->setObject('object2', 'ContainerTestClass', [ 'd:object1' ]);
        $this->assertInstanceOf('SimpleStructure\Container\Definition', $container->get('object2')->params[0]);
    }

    /**
     * Test definition getting and new instance creating
     */
    public function testDefinitionGettingAndNewInstanceCreating()
    {
        $container = new Container();
        $container->setObject('object', 'ContainerTestClass');
        $definition = $container->getDefinition('object');
        $this->assertInstanceOf('SimpleStructure\Container\Definition', $definition);
        $instance1 = $definition->create([ 20 ]);
        $this->assertInstanceOf('ContainerTestClass', $instance1);
        $this->assertEquals(20, $instance1->params[0]);
        $instance2 = $container->create('object', [ 30 ]);
        $this->assertInstanceOf('ContainerTestClass', $instance2);
        $this->assertEquals(30, $instance2->params[0]);
    }

    /**
     * Test definition getting and new instance creating with predefined params
     */
    public function testDefinitionGettingAndNewInstanceCreatingWithPredefinedParams()
    {
        $container = new Container();
        $container->setObject('object', 'ContainerTestClass', [], [ 10 ]);
        $definition = $container->getDefinition('object');
        $this->assertInstanceOf('SimpleStructure\Container\Definition', $definition);
        $instance1 = $definition->create([ 20 ]);
        $this->assertInstanceOf('ContainerTestClass', $instance1);
        $this->assertEquals(10, $instance1->params[0]);
        $this->assertEquals(20, $instance1->params[1]);
        $instance2 = $container->create('object', [ 30 ]);
        $this->assertInstanceOf('ContainerTestClass', $instance2);
        $this->assertEquals(10, $instance2->params[0]);
        $this->assertEquals(30, $instance2->params[1]);
    }

    /**
     * Test scalar factory setting
     */
    public function testScalarFactorySetting()
    {
        $container = new Container();
        $container->setObject('factory', function ($ratio) {
            return 100 * $ratio;
        });
        $this->assertEquals(3300, $container->create('factory', [ 33 ]));
    }

    /**
     * Test singleton dependency in factory setting
     */
    public function testSingletonDependencyInFactorySetting()
    {
        $container = new Container();
        $container->setObject('object', 'ContainerTestClass', [], [ 10 ]);
        $container->setObject('factory', function (ContainerTestClass $object, $ratio) {
            return $object->iterate(0) * $ratio;
        }, [ 'object' ], [ 10 ]);
        $this->assertEquals(110, $container->create('factory'));
        $this->assertEquals(120, $container->create('factory'));
    }

    /**
     * Test dependency instance in factory setting
     */
    public function testDependencyInstanceInFactorySetting()
    {
        $container = new Container();
        $container->setObject('object', 'ContainerTestClass', [], [ 10 ]);
        $container->setObject('factory', function (ContainerTestClass $object, $ratio) {
            return $object->iterate(0) * $ratio;
        }, [ 'i:object' ]);
        $this->assertEquals(1100, $container->create('factory', [ 100 ]));
        $this->assertEquals(2200, $container->create('factory', [ 200 ]));
        $this->assertEquals(1100, $container->create('factory', [ 100 ]));
    }
}

class ContainerTestClass
{
    public $params;

    public function __construct(...$params)
    {
        $this->params = $params;
    }

    public function iterate($index)
    {
        $paramIndex = max(0, min((int) round($index), count($this->params) - 1));
        $this->params[$paramIndex]++;

        return $this->params[$paramIndex];
    }
}
