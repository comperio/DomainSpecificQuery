<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Expression;


use DSQ\Expression\BasicExpression;
use DSQ\Expression\BoundedChildrenTreeExpression;

class BoundedChildrenTreeExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddChild()
    {
        $expr = new BasicExpression('a');
        $tree = new BoundedChildrenTreeExpression('foo', array($expr, $expr), 2, 3);

        $tree->addChild($expr);
        $this->setExpectedException('\OverflowException');
        $tree->addChild($expr);
    }
}
 