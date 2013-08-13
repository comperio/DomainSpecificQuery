<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Comperio\Compiler;

use DSQ\Compiler\Compiler;
use DSQ\Expression\Expression;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\TreeExpression;

/**
 * Class UrlCompiler
 * @package DSQ\Comperio
 *
 * This class compile an expression to a querystring-like array.
 * The format of the array reflects the one used in DiscoveryNG
 * @link http://www.comperio.it/soluzioni/discoveryng/panoramica/
 */
class SimpleUrlCompiler extends UrlCompiler
{
    /**
     * {@inheritdoc}
     */
    public function compile(Expression $tree)
    {
        if ($tree->getValue() != 'and')
            throw new OutOfBoundsExpressionException("Root expression must be an and expression");

        $expressionAry = $this->treeToAry($tree);
        $dump = array();

        $this
            ->sanitize($expressionAry)
            ->dumpSubtree('and', $expressionAry[0], $dump)
            ->dumpSubtree('not', $expressionAry[1], $dump)
        ;

        return $dump;
    }

    /**
     * @param $expectedOp
     * @param array $expressionAry
     * @param array $dump The dumped subtree will be added to this array
     * @return $this The current instance
     * @throws OutOfBoundsExpressionException
     */
    private function dumpSubtree($expectedOp, array $expressionAry, array &$dump)
    {
        $localFieldsCount = array();
        list($op, $childrenAry) = $expressionAry;

        if ($op != $expectedOp)
            throw new OutOfBoundsExpressionException("First level subtree do not match the expected operator (it is \"$op}\", it should be \"$expectedOp\")");

        $prefix = $expectedOp == 'not' ? '-' : '';

        foreach ($childrenAry as $fieldValuePair) {
            list($field, $value) = $fieldValuePair;
            $count = $this->fieldsCount[$op][$field];
            if (!isset($localFieldsCount[$op][$field]))
                $localFieldsCount[$op][$field] = 0;
            $localFieldsCount[$op][$field]++;
            $suffix = $count == 1 ? '' : "_{$localFieldsCount[$op][$field]}";
            $dump["$prefix$field$suffix"] = $value;
        }

        return $this;
    }

    /**
     * Sanitize the expression array putting empty and or not expressions
     * when necessary
     *
     * @param array $expressionAry  An array returned by treeToAry()
     * @see treeToAry()
     * @return $this
     */
    private function sanitize(array &$expressionAry)
    {
        if (count($expressionAry) == 0) {
            $expressionAry[] = array('and', array());
        }

        if (count($expressionAry) == 1) {
            if ('and' == $expressionAry[0][0]) {
                $expressionAry[] = array('not', array());
            } else {
                array_unshift($expressionAry, array('and', array()));
            }
        }

        return $this;
    }
} 