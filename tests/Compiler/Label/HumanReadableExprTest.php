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

class HumanReadableExprTest extends \PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $hrExpr = new HumanReadableExpr('foo', 'bar');

        $this->assertEquals(array('foo', 'bar'), $hrExpr->toArray());
    }

    public function testToArrayWithNestedValue()
    {
        $hrExpr = new HumanReadableExpr('foo', array(new HumanReadableExpr('a', 'b'), new HumanReadableExpr('c', 'd')));

        $this->assertEquals(array('foo', array(array('a', 'b'), array('c', 'd'))), $hrExpr->toArray());
    }

    public function testToString()
    {
        $hrExpr = new HumanReadableExpr('foo', 'bar');

        $this->assertEquals('foo: bar', $hrExpr->toString());
        $this->assertEquals('    foo: bar', $hrExpr->toString(1));
        $this->assertEquals('        foo: bar', $hrExpr->toString(2));
    }

    public function testToStringWithNestedVals()
    {
        $hrExpr = new HumanReadableExpr('foo', array(new HumanReadableExpr('a', 'b'), new HumanReadableExpr('c', 'd')));
        $expected = <<<EOF
foo:
    a: b
    c: d
EOF;
        $this->assertEquals($expected, (string) $hrExpr);
    }
}
 