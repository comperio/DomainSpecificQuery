<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Expression\Builder;


use DSQ\Expression\Builder\ExpressionBuilder;
use DSQ\Expression\TreeExpression;

class ExpressionBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testContructorSetContextToATreeExpression()
    {
        $b = new ExpressionBuilder;
        $this->assertEquals(new TreeExpression('and'), $b->get());

        $b = new ExpressionBuilder('or');
        $this->assertEquals(new TreeExpression('or'), $b->get());
    }

    public function testConstructorProcessesSet()
    {
        $b = new ExpressionBuilder;
        $proc = $b->getProcesses();

        $this->assertInstanceOf('DSQ\Expression\Builder\TreeProcess', $proc['and']);
        $this->assertInstanceOf('DSQ\Expression\Builder\TreeProcess', $proc['or']);
        $this->assertInstanceOf('DSQ\Expression\Builder\TreeProcess', $proc['not']);
        $this->assertInstanceOf('DSQ\Expression\Builder\BinaryProcess', $proc['binary']);
        $this->assertInstanceOf('DSQ\Expression\Builder\FieldProcess', $proc['field']);
        $this->assertInstanceOf('DSQ\Expression\Builder\ValueProcess', $proc['value']);
    }
}
 