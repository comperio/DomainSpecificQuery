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
abstract class UrlCompiler implements Compiler
{
    protected $fieldsCount;

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
        $this->fieldsCount = array();
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
    private function subtreeToAry(TreeExpression $tree)
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
    private function fieldToAry(BinaryExpression $field)
    {
        if ($field->getValue() != '=')
            throw new OutOfBoundsExpressionException("Field Expression operand is not \"=\" (it is \"{$field->getValue()}\")");

        return array((string) $field->getLeft()->getValue(), (string) $field->getRight()->getValue());
    }
} 