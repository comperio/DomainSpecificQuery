<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Comperio;

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
class UrlCompiler implements Compiler
{
    private $fieldsCount;

    /**
     * {@inheritdoc}
     */
    public function compile(Expression $tree)
    {
        if ($tree->getValue() != 'and')
            throw new OutOfBoundsExpressionException("Root expression must be an and expression");

        $this->fieldsCount = array();
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
     * {@inheritdoc}
     */
    public function transform($expression)
    {
        if ($expression instanceof Expression)
            return $this->compile($expression);

        return $expression;
    }

    /**
     * Returns true if all children of $tree fulfill $condition
     *
     * @param TreeExpression $tree
     * @param callable $condition
     * @return bool
     */
    protected function all(TreeExpression $tree, $condition)
    {
        foreach ($tree->getChildren() as $child) {
            if (!$condition($child))
                return false;
        }
        return true;
    }

    /**
     * Convert a TreeExpression to an array of the kind
     * <code>
     * [
     *      [operator1, [[field1, value1], [field2, value2], ...]],
     *      [operator2, [[field3, value3], [field4, value4], ...]],
     *      ...
     * ]
     * </code>
     * @param TreeExpression $tree
     * @return array
     */
    protected function treeToAry(TreeExpression $tree)
    {
        $ary = array();

        foreach ($tree->getChildren() as $child) {
            $ary[] = array((string) $child->getValue(), $this->subtreeToAry($child));
        }

        return $ary;
    }

    /**
     * Convert a subtree to an array of the kind
     * <code>
     * [
     *      [fieldname1, value1],
     *      [fieldname2, value2],
     *      ...
     * ]
     * </code>
     * It also updates the @see $fieldsCount field
     *
     * @param TreeExpression $tree
     * @return array
     */
    protected function subtreeToAry(TreeExpression $tree)
    {
        $ary = array();
        $op = $tree->getValue();

        foreach ($tree->getChildren() as $child) {
            $ary[] = $this->fieldToAry($child);
            $this->fieldsCount[$op][(string) $child->getLeft()->getValue()]++;
        }

        return $ary;
    }

    /**
     * Convert a field expression to an array of the kind
     * <code>
     *      [fieldname, value]
     * </code>
     * @param BinaryExpression $field
     * @return array
     * @throws OutOfBoundsExpressionException Thrown when the operator is not '='
     */
    protected function fieldToAry(BinaryExpression $field)
    {
        if ($field->getValue() != '=')
            throw new OutOfBoundsExpressionException("Field Expression operand is not \"=\" (it is \"{$field->getValue()}\")");

        return array((string) $field->getLeft()->getValue(), (string) $field->getRight()->getValue());
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