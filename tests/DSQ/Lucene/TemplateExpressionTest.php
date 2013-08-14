<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Lucene;


use DSQ\Lucene\TemplateExpression;
use DSQ\Lucene\TermExpression;

class TemplateExpressionTest extends \PHPUnit_Framework_TestCase
{

    public function testHasPrecedence()
    {
        $expr = new TemplateExpression('');

        $this->assertFalse($expr->hasPrecedence(new TermExpression('a')));
    }

    public function testToStringWithScalarValue()
    {
        $template = "field1:{} OR field2:{}";
        $expr = new TemplateExpression($template, 'val');

        $this->assertEquals("field1:val OR field2:val", (string) $expr);

        $expr->setBoost(2.2);
        $this->assertEquals("(field1:val OR field2:val)^2.2", (string) $expr);
    }

    public function testToString()
    {
        $template = "field1:{a.b} OR field3:{a.d} OR field2:{c}";
        $value = array('a' => array('b' => 'foo', 'd' => 'bar'), 'c' => 'apples');
        $expr = new TemplateExpression($template, $value);

        $this->assertEquals("field1:foo OR field3:bar OR field2:apples", (string) $expr);

        $expr->setBoost(3.14);
        $this->assertEquals("(field1:foo OR field3:bar OR field2:apples)^3.14", (string) $expr);
    }
}
 