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


use DSQ\Lucene\PhraseExpression;

class PhraseExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $expression = new PhraseExpression('foo " bar');

        $this->assertEquals('"foo \" bar"', (string) $expression);
    }

    public function testSlope()
    {
        $expression = new PhraseExpression('my phrase', 12);

        $this->assertEquals('"my phrase"~12', (string) $expression);

        $expression = new PhraseExpression('my phrase', 12.54);

        $this->assertEquals('"my phrase"~12.54', (string) $expression);
    }
}
 