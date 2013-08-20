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


use DSQ\Lucene\PureExpression;

class PureExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testHasPrecedence()
    {
        $expr = new PureExpression('mah');

        $this->assertFalse($expr->hasPrecedence(new PureExpression('foo')));
    }

    public function testToString()
    {
        $value = 'foo:bar and baz:"foo bar"';
        $expr = new PureExpression($value);

        $this->assertEquals($value, (string) $expr);
    }
}
 