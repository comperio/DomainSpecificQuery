<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Compiler\StringCompiler;


use DSQ\Compiler\StringCompiler\StringCompiler;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\TreeExpression;
use DSQ\Expression\FieldExpression;

class StringCompilerTest extends \PHPUnit_Framework_TestCase
{
    public function testTreeExpression()
    {
        $comp = new StringCompiler();

        $tree = new TreeExpression('+');
        $tree->setChildren(array(1, 2, 3, 4));

        $this->assertEquals("1 + 2 + 3 + 4", $comp->treeExpression($tree, $comp));
    }

    public function testTreeExpressionWithSubTrees()
    {
        $comp = new StringCompiler();

        $tree = new TreeExpression('*');
        $tree
            ->addChild(2)
            ->addChild($sum = new TreeExpression('+'))
        ;

        $sum->setChildren(array('a', 'b'));

        $this->assertEquals("2 * (a + b)", $comp->treeExpression($tree, $comp));

        $sum->setChildren(array('a'));
        $this->assertEquals("2 * a", $comp->treeExpression($tree, $comp));
    }

    public function testFieldExpression()
    {
        $comp = new StringCompiler();
        $field = new FieldExpression('foo', 'bar');

        $this->assertEquals('foo: bar', $comp->fieldExpression($field, $comp));

        $field->setValue("bar baz");
        $this->assertEquals('foo: "bar baz"', $comp->fieldExpression($field, $comp));
    }

    public function testBinaryExpression()
    {
        $comp = new StringCompiler();

        $binary = new BinaryExpression('+', 'a', 'b');

        $this->assertEquals("a + b", $comp->binaryExpression($binary, $comp));
    }

    public function testBinaryExpressionWithSubexpression()
    {
        $comp = new StringCompiler();

        $binary = new BinaryExpression('*', 'a', new BinaryExpression('+', 'b', 'c'));

        $this->assertEquals("a * (b + c)", $comp->binaryExpression($binary, $comp));
    }
}
 