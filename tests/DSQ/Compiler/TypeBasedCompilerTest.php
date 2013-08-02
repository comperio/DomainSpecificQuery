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

    protected $transformationFoo;
    protected $transformationBar;
    protected $transformationNic;

    public function setUp()
    {
        $this->compiler = new TypeBasedCompiler;

        $this->transformationFoo = function(Expression $exp, TypeBasedCompiler $c) {
            return 'foo:' . spl_object_hash($c) . '-' . spl_object_hash($exp);
        };

        $this->transformationBar = function(Expression $exp, TypeBasedCompiler $c) {
            return 'bar:' . spl_object_hash($c) . '-' . spl_object_hash($exp);
        };

        $this->transformationNic = function(Expression $exp, TypeBasedCompiler $c) {
            return 'nic:' . spl_object_hash($c) . '-' . spl_object_hash($exp);
        };

        $this->compiler
            ->registerTransformation($this->transformationNic, 'DSQ\Expression\BasicExpression', 'nic')
            ->registerTransformation($this->transformationFoo, '*', 'foo')
            ->registerTransformation($this->transformationBar, 'DSQ\Expression\BasicExpression', '*')
        ;
    }

    public function testRegisterAndGetTransformation()
    {
        $compiler = $this->compiler;

        $exp1 = new TreeExpression('ciao', 'foo');
        $exp2 = new BasicExpression('ciao', 'bar');
        $exp3 = new BasicExpression('ciao', 'nic');

        $this->assertEquals(call_user_func($this->transformationFoo, $exp1, $compiler), $compiler->compile($exp1));
        $this->assertEquals(call_user_func($this->transformationBar, $exp2, $compiler), $compiler->compile($exp2));
        $this->assertEquals(call_user_func($this->transformationNic, $exp3, $compiler), $compiler->compile($exp3));
    }

    public function testRegisterTransformationWithArrayClassesAndTypes()
    {
        $compiler = new TypeBasedCompiler;

        $compiler->registerTransformation($transf = function() {return 'foo'; }, array('DSQ\Expression\BasicExpression', 'DSQ\Expression\TreeExpression'), 'type');

        $this->assertEquals($transf, $compiler->getTransformation('DSQ\Expression\BasicExpression', 'type'));
        $this->assertEquals($transf, $compiler->getTransformation('DSQ\Expression\TreeExpression', 'type'));

        $compiler = new TypeBasedCompiler;
        $compiler->registerTransformation($transf = function() {return 'foo'; }, 'MyClass', array('type1', 'type2'));
        $this->assertEquals($transf, $compiler->getTransformation('MyClass', 'type1'));
        $this->assertEquals($transf, $compiler->getTransformation('MyClass', 'type2'));

        $compiler = new TypeBasedCompiler;
        $compiler->registerTransformation($transf = function() {return 'foo'; }, array('Class1', 'Class2'), array('type1', 'type2'));
        $this->assertEquals($transf, $compiler->getTransformation('Class1', 'type1'));
        $this->assertEquals($transf, $compiler->getTransformation('Class1', 'type2'));
        $this->assertEquals($transf, $compiler->getTransformation('Class2', 'type1'));
        $this->assertEquals($transf, $compiler->getTransformation('Class2', 'type2'));
    }

    public function testGetTransformationThrowsAnExceptionWhenRequestingAnUnregistedTransformation()
    {
        $this->setExpectedException('DSQ\Compiler\UnregisteredTransformationException');
        $this->compiler->getTransformation('*', 'baz');

        $this->setExpectedException('DSQ\Compiler\UnregisteredTransformationException');
        $this->compiler->getTransformation('baz', '*');
    }

    /**
     * @expectedException \DSQ\Compiler\InvalidTransformationException
     */
    public function testRegisterTransformationWithInvalidArgumentThrowsAnException()
    {
        $this->compiler->registerTransformation('foo', 'alksdjhalksdjh', 'asdasdasd');
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

        $this->assertEquals(call_user_func($this->transformationFoo, $exp1, $this->compiler), $compiler->transform($exp1));
    }
}