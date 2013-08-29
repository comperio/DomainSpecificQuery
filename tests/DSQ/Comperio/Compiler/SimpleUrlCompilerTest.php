<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */
use DSQ\Comperio\Compiler\SimpleUrlCompiler;
use DSQ\Expression\TreeExpression;
use DSQ\Expression\FieldExpression;

class SimpleUrlCompilerTest extends PHPUnit_Framework_TestCase
{
    public function testCompileWithEmptyTree()
    {
        $compiler = new SimpleUrlCompiler;

        $this->assertEquals(array(), $compiler->compile(new TreeExpression('and')));
    }

    public function testCompileWithOnlyOneConditionPerTypeCondition()
    {
        $compiler = new SimpleUrlCompiler;

        //One and condition
        $expr = new TreeExpression('and');
        $expr->addChild($and = new TreeExpression('and'));

        $and
            ->addChild(new FieldExpression('foo', 'bar'))
        ;

        $this->assertEquals(array('foo' => 'bar'), $compiler->compile($expr));

        //One and condition and one not condition
        $expr->addChild($not = new TreeExpression('not'));
        $not->addChild(new FieldExpression('foo', 'baz'));

        $this->assertEquals(array('foo' => 'bar', '-foo' => 'baz'), $compiler->compile($expr));

        //One not condition
        $expr = new TreeExpression('and');
        $expr->addChild($not = new TreeExpression('not'));
        $not->addChild(new FieldExpression('foo', 'bar'));
        $this->assertEquals(array('-foo' => 'bar'), $compiler->compile($expr));

    }

    public function testCompileWithMultipleConditions()
    {
        $compiler = new SimpleUrlCompiler;

        $expr = new TreeExpression('and');
        $expr->addChild($and = new TreeExpression('and'));

        $and
            ->addChild(new FieldExpression('foo', 'bar'))
            ->addChild(new FieldExpression('foo', 'baz'))
            ->addChild(new FieldExpression('nic', 'mart'))
        ;

        $this->assertEquals(array('foo_1' => 'bar', 'foo_2' => 'baz', 'nic' => 'mart'), $compiler->compile($expr));

        $expr->addChild($not = new TreeExpression('not'));
        $not
            ->addChild(new FieldExpression('foo', 'bar'))
            ->addChild(new FieldExpression('foo', 'baz'))
            ->addChild(new FieldExpression('nic', 'mart'));

        $this->assertEquals(
            array(
                'foo_1' => 'bar',
                'foo_2' => 'baz',
                'nic' => 'mart',
                '-foo_1' => 'bar',
                '-foo_2' => 'baz',
                '-nic' => 'mart'
            ),
            $compiler->compile($expr));
    }

    public function testCompileWithAnArrayValue()
    {
        $compiler = new SimpleUrlCompiler;

        $expr = new TreeExpression('and');
        $expr->addChild($and = new TreeExpression('and'));

        $and
            ->addChild(new FieldExpression('foo', array('a' => 'b', 'c' => 'd')))
        ;

        $this->assertEquals(array('foo' => array('a' => 'b', 'c' => 'd')), $compiler->compile($expr));
    }

    /**
     * @expectedException DSQ\Comperio\Compiler\OutOfBoundsExpressionException
     */
    public function testExceptionIsThrownWhenMainTreeIsNotAnAndTree()
    {
        $compiler = new SimpleUrlCompiler;
        $tree = new TreeExpression('xor');

        $compiler->compile($tree);
    }

    /**
     * @expectedException DSQ\Comperio\Compiler\OutOfBoundsExpressionException
     */
    public function testExceptionIsThrownWhenFirstLevelSubtreesAreNotOfTheExpectedOperator()
    {
        $compiler = new SimpleUrlCompiler;
        $tree = new TreeExpression('and');

        $tree->addChild(new TreeExpression('xor'));
        $compiler->compile($tree);
    }
}
 