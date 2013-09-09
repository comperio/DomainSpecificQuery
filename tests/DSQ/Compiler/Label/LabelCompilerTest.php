<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Compiler\Label;


use DSQ\Compiler\Label\HumanReadableExpr;
use DSQ\Compiler\Label\LabelCompiler;
use DSQ\Expression\TreeExpression;
use DSQ\Expression\FieldExpression;

class LabelCompilerTest extends \PHPUnit_Framework_TestCase
{
    public function testFieldMap()
    {
        $c = new LabelCompiler;

        $field = new FieldExpression('foo', 'bar');

        $this->assertEquals(new HumanReadableExpr('foo', 'bar'), $c->compile($field));
    }

    public function testTreeMap()
    {
        $c = new LabelCompiler();

        $tree = new TreeExpression('and');
        $tree
            ->addChild($child0 = new FieldExpression('foo', 'bar'))
            ->addChild($child1 = new FieldExpression('bel', 'mar'))
            ->addChild($child2 = new FieldExpression('ero', 'uno'))
        ;

        $expected = new HumanReadableExpr('and', array(
            new HumanReadableExpr('foo', 'bar'),
            new HumanReadableExpr('bel', 'mar'),
            new HumanReadableExpr('ero', 'uno'),
        ));

        $this->assertEquals($expected, $c->compile($tree));
    }
}
 