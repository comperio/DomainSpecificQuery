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

    public function testFieldMapWithArrayValue()
    {
        $c = new LabelCompiler;
        $ary = array('a' => array('b', 'v', 'u'));
        $field = new FieldExpression('foo', $ary);

        $this->assertEquals(new HumanReadableExpr('foo', json_encode($ary)), $c->compile($field));
    }

    public function testFieldWithFieldCallbackMap()
    {
        $c = new LabelCompiler('strtoupper');

        $field = new FieldExpression('foo', 'bar');

        $this->assertEquals(new HumanReadableExpr('FOO', 'bar'), $c->compile($field));
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

    public function testTreeMapDoesApplyFieldnameCallback()
    {
        $c = new LabelCompiler(function(){ return 'hello'; });

        $tree = new TreeExpression('and');
        $this->assertEquals(new HumanReadableExpr('and', array()), $c->compile($tree));
    }

    public function testGetFieldLabel()
    {
        $c = new LabelCompiler();
        $this->assertEquals('foo', $c->getFieldLabel('foo'));

        $c = new LabelCompiler(function(){ return 'hello'; });
        $this->assertEquals('hello', $c->getFieldLabel('foo'));
    }
}
 