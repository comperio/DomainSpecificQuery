<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Expression\Builder;


use DSQ\Expression\BinaryExpression;
use DSQ\Expression\Expression;

class BinaryBuilder extends AbstractBuilder
{
    function createExpression(&$pushOnStack, $operator = '=', $left = null, $right = null, $type = null)
    {
        $pushOnStack = !isset($right);

        return new BinaryExpression($operator, $left, $right, $type);
    }

    function addChild(Expression $expr)
    {
        $currentExp = $this->getExpression();

        if ($currentExp->getLeft()->getValue() === null)
            $currentExp->setLeft($expr);
        else
            $currentExp->setRight($expr);
    }

    /**
     * {@inheritdoc}
     */
    function start($operator = '=', $left = null, $right = null, $type = null)
    {
        $this->stack[] = new Context(
            new BinaryExpression($operator, $left, $right, $type),
            $this,
            array($left, $right)
        );
    }

    /**
     * {@inheritdoc}
     */
    function manipulate()
    {
        list($left, $right) = $this->context()->arguments;

        $this->context()->object
            ->setLeft($left)
            ->setRight($right);
    }


}