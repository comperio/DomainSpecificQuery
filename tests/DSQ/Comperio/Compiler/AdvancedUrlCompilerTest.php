<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */
use DSQ\Comperio\Compiler\AdvancedUrlCompiler;
use DSQ\Expression\TreeExpression;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\FieldExpression;

class AdvancedUrlCompilerTest extends PHPUnit_Framework_TestCase
{
    public function testCompileWithEmptyTree()
    {
        $compiler = new AdvancedUrlCompiler;

        $this->assertEquals(array(), $compiler->compile(new TreeExpression('and')));
    }

    public function testCompile()
    {
        $compiler = new AdvancedUrlCompiler;

                $expr = new TreeExpression('and');
                $expr
                    ->addChild($and = new TreeExpression('and'))
                    ->addChild($or1 = new TreeExpression('or'))
                    ->addChild($or2 = new TreeExpression('or'))
                ;

                $and
                    ->addChild(new FieldExpression('foo', 'bar'))
                    ->addChild(new FieldExpression('foo', 'baz'))
                ;

                $or1
                    ->addChild(new FieldExpression('bim', 'bum'))
                    ->addChild(new FieldExpression('bam', 'bang'))
                ;

                $or2
                    ->addChild(new FieldExpression('nothing', 'can'))
                    ->addChild(new FieldExpression('be', 'wrong'))
                ;

                $this->assertEquals(
                    array(
                        'op_1' => 'and',
                        'field_1' => 'foo', 'value_1' => 'bar', 'lop_1' => '1',
                        'field_2' => 'foo', 'value_2' => 'baz', 'lop_2' => '1',

                        'op_2' => 'or',
                        'field_3' => 'bim', 'value_3' => 'bum', 'lop_3' => '2',
                        'field_4' => 'bam', 'value_4' => 'bang', 'lop_4' => '2',

                        'op_3' => 'or',
                        'field_5' => 'nothing', 'value_5' => 'can', 'lop_5' => '3',
                        'field_6' => 'be', 'value_6' => 'wrong', 'lop_6' => '3',
                    ),
                    $compiler->compile($expr));
    }

    /**
     * @expectedException DSQ\Comperio\Compiler\OutOfBoundsExpressionException
     */
    public function testExceptionIsThrownWhenMainTreeIsNotAnAndTree()
    {
        $compiler = new AdvancedUrlCompiler;
        $tree = new TreeExpression('xor');

        $compiler->compile($tree);
    }
}
 