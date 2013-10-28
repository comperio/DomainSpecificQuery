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

use DSQ\Compiler\MatcherCompiler;
use DSQ\Compiler\TypeBasedCompiler;
use DSQ\Compiler\UnregisteredTransformationException;
use DSQ\Expression\BasicExpression;
use DSQ\Expression\Expression;
use DSQ\Expression\FieldExpression;
use DSQ\Expression\TreeExpression;

/**
 * Unit tests for class FirstClass
 */
class MatcherCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MatcherCompiler
     */
    protected $compiler;

    protected $mapFoo;
    protected $mapBar;
    protected $mapNic;

    public function setUp()
    {
        $this->compiler = new MatcherCompiler;

        $this->mapFoo = function(Expression $exp, MatcherCompiler $c) {
            return 'foo:' . spl_object_hash($c) . '-' . spl_object_hash($exp);
        };

        $this->mapBar = function(Expression $exp, MatcherCompiler $c) {
            return 'bar:' . spl_object_hash($c) . '-' . spl_object_hash($exp);
        };

        $this->mapNic = function(Expression $exp, MatcherCompiler $c) {
            return 'nic:' . spl_object_hash($c) . '-' . spl_object_hash($exp);
        };

        $this->compiler
            ->mapByClassAndType('DSQ\Expression\BasicExpression', 'nic', $this->mapNic)
            ->map('foo', $this->mapFoo)
            ->mapByClass('DSQ\Expression\BasicExpression', $this->mapBar)
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
        $compiler = new MatcherCompiler;

        $compiler->map(
            'type', $map = function () {
                return 'foo';
            }
        );

        $this->assertEquals($map, $compiler->getMap('type'));
        $this->assertEquals($map, $compiler->getMap('type'));

        $compiler = new MatcherCompiler;
        $compiler->map(
            array('type1', 'type2'), $map = function () {
                return 'foo';
            }
        );
        $this->assertEquals($map, $compiler->getMap('type1'));
        $this->assertEquals($map, $compiler->getMap('type2'));

        $compiler = new MatcherCompiler;
        $compiler->map(
            array('type1', 'type2'), $map = function () {
                return 'foo';
            }
        );
        $this->assertEquals($map, $compiler->getMap('type1'));
        $this->assertEquals($map, $compiler->getMap('type2'));
        $this->assertEquals($map, $compiler->getMap('type1'));
        $this->assertEquals($map, $compiler->getMap('type2'));
    }

    public function testGetTransformationThrowsAnExceptionWhenRequestingAnUnregistedTransformation()
    {
        $this->setExpectedException('DSQ\Compiler\UnregisteredTransformationException');
        $this->compiler->getMap('baz', '*');

        $this->setExpectedException('DSQ\Compiler\UnregisteredTransformationException');
        $this->compiler->getMap('*', 'baz');
    }

    /**
     * @expectedException \DSQ\Compiler\InvalidTransformationException
     */
    public function testRegisterTransformationWithInvalidArgumentThrowsAnException()
    {
        $this->compiler->map('asdasdasd', 'foo');
    }

    /**
     * @expectedException DSQ\Compiler\UncompilableValueException
     */
    public function testCompileThrowsAnExceptionWhenNoMapMatches()
    {
        $this->compiler->compile(new FieldExpression('not', 'compilable'));
    }
}