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

    public function testCanTransform()
    {
        $this->assertTrue($this->compiler->canTransform('MyClass', 'foo'));
        $this->assertTrue($this->compiler->canTransform('DSQ\Expression\BasicExpression'));
        $this->assertTrue($this->compiler->canTransform('DSQ\Expression\BasicExpression', 'poo'));
        $this->assertFalse($this->compiler->canTransform('*', 'moo'));
        $this->assertFalse($this->compiler->canTransform('MyClass', '*'));
    }
}