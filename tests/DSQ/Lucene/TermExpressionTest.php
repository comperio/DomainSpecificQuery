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

use DSQ\Lucene\TermExpression;

class TermExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $expression = new TermExpression('foo');

        $this->assertEquals('foo', (string) $expression);
    }

    public function testToStringEscapesValue()
    {
        $expression = new TermExpression(':"()[]');

        $this->assertEquals('\:"\(\)\[\]', (string) $expression);
    }

    public function testBoosting()
    {
        $expression = new TermExpression('expr', 12.2);

        $this->assertEquals('expr^12.2', (string) $expression);
    }

    public function testHasPrecedenceReturnsFalseOnlyIfThereAreSpaces()
    {
        $expr = new TermExpression('foo');

        $this->assertTrue($expr->hasPrecedence(null));

        $expr->setValue('foo bar baz');

        $this->assertFalse($expr->hasPrecedence(null));
    }

    public function testBoostSuffixWhenCurrentLocaleHasNotDotAsDecimalSeparator()
    {
        $oldLocale = setlocale(LC_ALL, 0);
        setlocale(LC_ALL, 'it_IT');

        $expr = new TermExpression('foo', 2.13);
        $this->assertEquals('foo^2.13', (string) $expr);

        setlocale(LC_ALL, $oldLocale);
    }
}
 