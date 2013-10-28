<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Expression;


use DSQ\Expression\FieldExpression;

class FieldExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $field = new FieldExpression('foo', 'bar', '=');

        $this->assertEquals('foo', $field->getField());
        $this->assertEquals('bar', $field->getValue());
        $this->assertEquals('foo', $field->getType());

        $field = new FieldExpression('foo', 'bar', '=', 'baz');
        $this->assertEquals('baz', $field->getType());
    }

    public function testSetAndGetField()
    {
        $field = new FieldExpression('foo', 'bar', '=');
        $field->setField('field');
        $this->assertEquals('field', $field->getField());
    }
}
 