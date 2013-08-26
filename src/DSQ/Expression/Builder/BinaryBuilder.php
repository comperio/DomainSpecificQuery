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
use Building\Context;
use Building\AbstractBuilder;

class BinaryBuilder extends AbstractBuilder
{
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