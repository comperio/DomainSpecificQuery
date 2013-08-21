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


use DSQ\Comperio\Compiler\Map\StandardNumberMap;
use DSQ\Expression\BinaryExpression;

class StandardNumberMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StandardNumberMap
     */
    protected $map;

    /**
     * @var BinaryExpression
     */
    protected $expr;

    public function setUp()
    {
        $this->map = new StandardNumberMap(array('nic' => 'mart', 'gab' => 'med'));
        $this->expr = new BinaryExpression('=', 'foo', 'bar');
    }

    public function testWithScalarValueShouldReturnsAllNumbers()
    {
        $this->assertEquals('mart:bar OR med:bar', (string) $this->map->__invoke($this->expr));
    }

    public function testWithArrayValueWithSomethingMissing()
    {
        $this->expr->getRight()->setValue(array('subfield' => '', 'value' => 'bar'));
        $this->assertEquals('mart:bar OR med:bar', (string) $this->map->__invoke($this->expr));

        $this->expr->getRight()->setValue(array('value' => 'bar'));
        $this->assertEquals('mart:bar OR med:bar', (string) $this->map->__invoke($this->expr));

        $this->expr->getRight()->setValue(array('subfield' => 'none', 'value' => 'bar'));
        $this->assertEquals('mart:bar OR med:bar', (string) $this->map->__invoke($this->expr));
    }

    public function testSingleNumberValue()
    {
        $this->expr->getRight()->setValue(array('subfield' => 'nic', 'value' => 'bar'));
        $this->assertEquals('mart:bar', (string) $this->map->__invoke($this->expr));
    }
}
 