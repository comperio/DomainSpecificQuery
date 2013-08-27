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


use Building\Context;
use Building\BuildProcess;
use DSQ\Expression\BinaryExpression;

class BinaryProcess implements BuildProcess
{
    /**
     * {@inheritdoc}
     */
    public function build(Context $context, $operator = '=', $left = null, $right = null, $type = null)
    {
        $binary = new BinaryExpression($operator, $left, $right, $type);
        $context->process->subvalueBuilded($context, $binary);

        if (!isset($right))
            return new Context($binary, $this);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function subvalueBuilded(Context $context, $expression)
    {
        /** @var BinaryExpression $currExpr */
        $currExpr = $context->object;
        if ($currExpr->getLeft()->getValue() === null)
            $currExpr->setLeft($expression);
        else
            $currExpr->setRight($expression);
    }

    /**
     * {@inheritdoc}
     */
    function processStart($operator = '=', $left = null, $right = null, $type = null)
    {
        $binary = new BinaryExpression($operator, $left, $right, $type);
        $this->addArgument($binary);

        $this->stack[] = new Context(
            $binary,
            $this,
            isset($right) ? array($left, $right) : array()
        );

        if ($right)
            return $this->processEnd();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    function processArgs()
    {
        list($left, $right) = $this->context()->arguments;

        $this->context()->object
            ->setLeft($left)
            ->setRight($right);
    }
}