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


use DSQ\Lucene\MatchAllExpression;

class MatchAllExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testHasPrecedence()
    {
        $expr = new MatchAllExpression;

        $this->assertTrue($expr->hasPrecedence(null));
    }

    public function testToString()
    {
        $expr = new MatchAllExpression;
        $this->assertEquals('*:*', (string) $expr);

        $expr->setBoost(1983.03);
        $this->assertEquals('*:*^1983.03', (string) $expr);
    }
}
 