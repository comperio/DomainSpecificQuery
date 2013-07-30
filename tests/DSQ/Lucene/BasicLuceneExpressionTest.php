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
}
 