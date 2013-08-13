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
 * Class AdvancedUrlCompiler
 * @package DSQ\Comperio
 *
 * This class compile an expression to a querystring-like array.
 * The format of the array reflects the one used in DiscoveryNG in the advanced search
 * @link http://www.comperio.it/soluzioni/discoveryng/panoramica/
 */
class AdvancedUrlCompiler extends UrlCompiler
{
    private $fieldIndex = 1;
    private $opIndex = 1;

    /**
     * {@inheritdoc}
     */
    public function compile(Expression $tree)
    {
        $this->fieldIndex = 1;
        $this->opIndex = 1;

        if ($tree->getValue() != 'and')
            throw new OutOfBoundsExpressionException("Root expression must be an and expression");

        $expressionsAry = $this->treeToAry($tree);
        $dump = array();

        foreach ($expressionsAry as $expressionAry) {
            $dump["op_{$this->opIndex}"] = $expressionAry[0];
            $this->dumpSubtree($expressionAry, $dump);
            $this->opIndex++;
        }

        return $dump;
    }

    /**
     * @param array $expressionAry
     * @param array $dump The dumped subtree will be added to this array
     * @return $this The current instance
     */
    private function dumpSubtree(array $expressionAry, array &$dump)
    {
        list($op, $childrenAry) = $expressionAry;

        foreach ($childrenAry as $fieldValuePair) {
            list($field, $value) = $fieldValuePair;
            $index = $this->fieldIndex;
            $dump["field_$index"] = $field;
            $dump["value_$index"] = $value;
            $dump["lop_$index"] = $this->opIndex;

            $this->fieldIndex++;
        }

        return $this;
    }
} 