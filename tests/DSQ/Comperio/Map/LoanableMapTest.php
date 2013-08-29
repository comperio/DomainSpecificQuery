<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Comperio\Map;


use DSQ\Comperio\Compiler\Map\LoanableMap;
use DSQ\Expression\FieldExpression;
use DSQ\Lucene\Compiler\LuceneCompiler;

class LoanableMapTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $map = new LoanableMap(new \DateTime('2013-07-21'));
        $expr = new FieldExpression('foo', 0);

        $this->assertEquals('*:* NOT mrc_d901_sl:[2013-07-22 TO *]', (string) $map($expr, new LuceneCompiler()));

        $expr->setValue(1);
        $this->assertEquals('*:* NOT mrc_d901_sl:[2013-07-23 TO *]', (string) $map($expr, new LuceneCompiler()));

        $expr->setValue(2);
        $this->assertEquals('*:* NOT mrc_d901_sl:[2013-07-29 TO *]', (string) $map($expr, new LuceneCompiler()));
    }
}
 