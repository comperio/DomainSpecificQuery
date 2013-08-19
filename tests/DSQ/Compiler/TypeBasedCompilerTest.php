<?php
/*
 * This file is part of DomainSpecificQuery.
 *
 * (c) 2013 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DSQ\Test\Compiler;

use DSQ\Compiler\TypeBasedCompiler;
use DSQ\Expression\BasicExpression;
use DSQ\Expression\Expression;
use DSQ\Expression\TreeExpression;

/**
 * Unit tests for class FirstClass
 */
class TypeBasedCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypeBasedCompiler
     */
    protected $compiler;

    protected $mapFoo;
    protected $mapBar;
    protected $mapNic;

    public function setUp()
    {
        $this->compiler = new TypeBasedCompiler;

        $this->mapFoo = function(Expression $exp, TypeBasedCompiler $c) {
            return 'foo:' . spl_object_hash($c) . '-' . spl_object_hash($exp);
        };

        $this->mapBar = function(Expression $exp, TypeBasedCompiler $c) {
            return 'bar:' . spl_object_hash($c) . '-' . spl_object_hash($exp);
        };

        $this->mapNic = function(Expression $exp, TypeBasedCompiler $c) {
            return 'nic:' . spl_object_hash($c) . '-' . spl_object_hash($exp);
        };

        $this->compiler
            ->map('nic:DSQ\Expression\BasicExpression', $this->mapNic)
            ->map('foo', $this->mapFoo)
            ->map('*:DSQ\Expression\BasicExpression', $this->mapBar)
        ;
    }

    public function testRegisterAndGetTransformation()
    {
        $compiler = $this->compiler;

        $exp1 = new TreeExpression('ciao', 'foo');
        $exp2 = new BasicExpression('ciao', 'bar');
        $exp3 = new BasicExpression('ciao', 'nic');

        $this->assertEquals(call_user_func($this->mapFoo, $exp1, $compiler), $compiler->compile($exp1));
        $this->assertEquals(call_user_func($this->mapBar, $exp2, $compiler), $compiler->compile($exp2));
        $this->assertEquals(call_user_func($this->mapNic, $exp3, $compiler), $compiler->compile($exp3));
    }

    public function testRegisterTransformationWithArrayClassesAndTypes()
    {
        $compiler = new TypeBasedCompiler;

        $compiler->map(
            'type', $map = function () {
                return 'foo';
            }
        );

        $this->assertEquals($map, $compiler->getMap('DSQ\Expression\BasicExpression', 'type'));
        $this->assertEquals($map, $compiler->getMap('DSQ\Expression\TreeExpression', 'type'));

        $compiler = new TypeBasedCompiler;
        $compiler->map(
            array('type1', 'type2'), $map = function () {
                return 'foo';
            }
        );
        $this->assertEquals($map, $compiler->getMap('MyClass', 'type1'));
        $this->assertEquals($map, $compiler->getMap('MyClass', 'type2'));

        $compiler = new TypeBasedCompiler;
        $compiler->map(
            array('type1', 'type2'), $map = function () {
                return 'foo';
            }
        );
        $this->assertEquals($map, $compiler->getMap('Class1', 'type1'));
        $this->assertEquals($map, $compiler->getMap('Class1', 'type2'));
        $this->assertEquals($map, $compiler->getMap('Class2', 'type1'));
        $this->assertEquals($map, $compiler->getMap('Class2', 'type2'));
    }

    public function testGetTransformationThrowsAnExceptionWhenRequestingAnUnregistedTransformation()
    {
        $this->setExpectedException('DSQ\Compiler\UnregisteredTransformationException');
        $this->compiler->getMap('*', 'baz');

        $this->setExpectedException('DSQ\Compiler\UnregisteredTransformationException');
        $this->compiler->getMap('baz', '*');
    }

    /**
     * @expectedException \DSQ\Compiler\InvalidTransformationException
     */
    public function testRegisterTransformationWithInvalidArgumentThrowsAnException()
    {
        $this->compiler->map('asdasdasd', 'foo');
    }

    public function testCanCompile()
    {
        $this->assertTrue($this->compiler->canCompile('MyClass', 'foo'));
        $this->assertTrue($this->compiler->canCompile('DSQ\Expression\BasicExpression'));
        $this->assertTrue($this->compiler->canCompile('DSQ\Expression\BasicExpression', 'poo'));
        $this->assertFalse($this->compiler->canCompile('*', 'moo'));
        $this->assertFalse($this->compiler->canCompile('MyClass', '*'));
    }

    public function testTransform()
    {
        $this->assertEquals('foo', $this->compiler->transform('foo'));

        $compiler = $this->compiler;
        $exp1 = new TreeExpression('ciao', 'foo');

        $this->assertEquals(call_user_func($this->mapFoo, $exp1, $this->compiler), $compiler->transform($exp1));
    }
}