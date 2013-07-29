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

    public function setUp()
    {
        $this->compiler = new TypeBasedCompiler;

        $this->transformationFoo = function(Expression $exp, TypeBasedCompiler $c) {
            return 'foo:' . spl_object_hash($c) . '-' . spl_object_hash($exp);
        };

        $this->transformationBar = function(Expression $exp, TypeBasedCompiler $c) {
            return 'bar:' . spl_object_hash($c) . '-' . spl_object_hash($exp);
        };

        $this->compiler
            ->registerTransformation('foo', $this->transformationFoo)
            ->registerTransformation('bar', $this->transformationBar)
        ;
    }

    public function testRegisterAndGetTransformation()
    {
        $compiler = $this->compiler;

        $exp1 = new BasicExpression('ciao', 'foo');
        $exp2 = new BasicExpression('ciao', 'bar');

        $this->assertEquals(call_user_func($this->transformationFoo, $exp1, $compiler), $compiler->compile($exp1));
        $this->assertEquals(call_user_func($this->transformationBar, $exp2, $compiler), $compiler->compile($exp2));
    }

    /**
     * @expectedException \DSQ\Compiler\UnregisteredTransformationException
     */
    public function testGetTransformationThrowsAnExceptionWhenRequestingAnUnregistedTransformation()
    {
        $this->compiler->getTransformation('baz');
    }

    /**
     * @expectedException \DSQ\Compiler\InvalidTransformationException
     */
    public function testRegisterTransformationWithInvalidArgumentThrowsAnException()
    {
        $this->compiler->registerTransformation('foo', 'alksdjhalksdjh');
    }

    public function testHasTransformation()
    {
        $this->assertTrue($this->compiler->hasTransformation('foo'));
        $this->assertTrue($this->compiler->hasTransformation('bar'));
        $this->assertFalse($this->compiler->hasTransformation('poo'));
    }
}