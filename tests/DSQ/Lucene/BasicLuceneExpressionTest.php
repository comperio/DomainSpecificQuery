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

use DSQ\Lucene\BasicLuceneExpression;

class BasicLuceneExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $expression = new BasicLuceneExpression('foo');

        $this->assertEquals('foo', (string) $expression);
    }

    public function testToStringEscapesValue()
    {
        $expression = new BasicLuceneExpression(':"()[]');

        $this->assertEquals('\:"\(\)\[\]', (string) $expression);
    }

    public function testSetDeepValue()
    {
        $expr = new BasicLuceneExpression($first = new BasicLuceneExpression(new BasicLuceneExpression($last = new BasicLuceneExpression('value'))));

        $expr->setDeepValue('foo');

        $this->assertEquals('foo', $last->getValue());
        $this->assertEquals($first, $expr->getValue());
    }

    public function testSetAndGetBoost()
    {
        $expression = new BasicLuceneExpression('expr');
        $expression->setBoost(12.4);

        $this->assertSame(12.4, $expression->getBoost());

        $expression->setBoost(54);
        $this->assertSame(54.0, $expression->getBoost());
    }

    public function testBoosting()
    {
        $expression = new BasicLuceneExpression('expr', 12.2);

        $this->assertEquals('expr^12.2', (string) $expression);
    }

    public function testEscapeDoNothingOnLuceneExpressions()
    {
        $expr = new BasicLuceneExpression('foo');

        $this->assertEquals($expr, BasicLuceneExpression::escape($expr));
    }

    public function testEscapePhraseDoNothingOnLuceneExpressions()
    {
        $expr = new BasicLuceneExpression('foo');

        $this->assertEquals($expr, BasicLuceneExpression::escape_phrase($expr));
    }

    public function testHasPrecedenceReturnsFalseOnlyIfThereAreSpaces()
    {
        $expr = new BasicLuceneExpression('foo');

        $this->assertTrue($expr->hasPrecedence(null));

        $expr->setValue('foo bar baz');

        $this->assertFalse($expr->hasPrecedence(null));
    }
}
 