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


use DSQ\Lucene\RangeExpression;

class RangeExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testHasPrecedence()
    {
        $expr = new RangeExpression;
        $this->assertTrue($expr->hasPrecedence(null));
    }

    public function testToString()
    {
        $expr = new RangeExpression(10, 100);

        $this->assertEquals('[10 TO 100]', (string) $expr);

        $expr->setBoost(12.2);
        $this->assertEquals('[10 TO 100]^12.2', (string) $expr);

        $expr = new RangeExpression(':dontescapeme:', 100, 1.0, $includeLeft = true, $includeRight = false);
        $this->assertEquals('[:dontescapeme: TO 100}', (string) $expr);

        $expr = new RangeExpression;

        $this->assertEquals('[* TO *]', (string) $expr);
    }
}
 