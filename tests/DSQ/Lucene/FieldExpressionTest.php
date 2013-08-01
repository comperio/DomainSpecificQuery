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


use DSQ\Lucene\FieldExpression;

class FieldExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $field = new FieldExpression('field', 'value:[');

        $this->assertEquals('field:value\:\[', (string) $field);
    }

    public function testBoosting()
    {
        $field = new FieldExpression('field', 'value', 83.1);

        $this->assertEquals('field:value^83.1', (string) $field);
    }
}
 