<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */
use DSQ\Comperio\UrlDumper;
use DSQ\Expression\TreeExpression;
use DSQ\Expression\BinaryExpression;

class UrlDumperTest extends PHPUnit_Framework_TestCase
{
    public function testDumpWithEmptyTree()
    {
        $dumper = new UrlDumper;

        $this->assertEquals(array(), $dumper->dump(new TreeExpression('and')));
    }

    public function testDumpWithOnlyOneConditionPerTypeCondition()
    {
        $dumper = new UrlDumper;

        //One and condition
        $expr = new TreeExpression('and');
        $expr->addChild($and = new TreeExpression('and'));

        $and
            ->addChild(new BinaryExpression('=', 'foo', 'bar'))
        ;

        $this->assertEquals(array('foo' => 'bar'), $dumper->dump($expr));

        //One and condition and one not condition
        $expr->addChild($not = new TreeExpression('not'));
        $not->addChild(new BinaryExpression('=', 'foo', 'baz'));

        $this->assertEquals(array('foo' => 'bar', '-foo' => 'baz'), $dumper->dump($expr));

        //One not condition
        $expr = new TreeExpression('and');
        $expr->addChild($not = new TreeExpression('not'));
        $not->addChild(new BinaryExpression('=', 'foo', 'bar'));
        $this->assertEquals(array('-foo' => 'bar'), $dumper->dump($expr));

    }

    public function testDumpWithMultipleConditions()
    {
        $dumper = new UrlDumper;

                $expr = new TreeExpression('and');
                $expr->addChild($and = new TreeExpression('and'));

                $and
                    ->addChild(new BinaryExpression('=', 'foo', 'bar'))
                    ->addChild(new BinaryExpression('=', 'foo', 'baz'))
                    ->addChild(new BinaryExpression('=', 'nic', 'mart'))
                ;

                $this->assertEquals(array('foo_1' => 'bar', 'foo_2' => 'baz', 'nic' => 'mart'), $dumper->dump($expr));

                $expr->addChild($not = new TreeExpression('not'));
                $not
                    ->addChild(new BinaryExpression('=', 'foo', 'bar'))
                    ->addChild(new BinaryExpression('=', 'foo', 'baz'))
                    ->addChild(new BinaryExpression('=', 'nic', 'mart'));

                $this->assertEquals(
                    array(
                        'foo_1' => 'bar',
                        'foo_2' => 'baz',
                        'nic' => 'mart',
                        '-foo_1' => 'bar',
                        '-foo_2' => 'baz',
                        '-nic' => 'mart'
                    ),
                    $dumper->dump($expr));
    }

    /**
     * @expectedException DSQ\Comperio\OutOfBoundsExpressionException
     */
    public function testExceptionIsThrownWhenMainTreeIsNotAnAndTree()
    {
        $dumper = new UrlDumper;
        $tree = new TreeExpression('xor');

        $dumper->dump($tree);
    }

    /**
     * @expectedException DSQ\Comperio\OutOfBoundsExpressionException
     */
    public function testExceptionIsThrownWhenFirstLevelSubtreesAreNotOfTheExpectedOperator()
    {
        $dumper = new UrlDumper;
        $tree = new TreeExpression('and');

        $tree->addChild(new TreeExpression('xor'));
        $dumper->dump($tree);
    }
}
 