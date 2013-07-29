<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Compiler\StringCompiler;


use DSQ\Compiler\TypeBasedCompiler;
use DSQ\Expression\BasicExpression;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\TreeExpression;

/**
 * Class StringCompiler
 *
 * This is a simple compiler that converts expression using a math-style infix notation
 *
 * @package DSQ\Compiler\StringCompiler
 */
class StringCompiler extends TypeBasedCompiler
{
    public function __construct()
    {
        $this
            ->registerTransformation(array($this, 'basicExpression'), '*', '*')
            ->registerTransformation(array($this, 'binaryExpression'), 'DSQ\Expression\BinaryExpression')
            ->registerTransformation(array($this, 'treeExpression'), 'DSQ\Expression\TreeExpression')
            ->registerTransformation(array($this, 'binaryExpressionWithNoSpaces'), 'DSQ\Expression\BinaryExpression', '^')
        ;
    }

    /**
     * @param TreeExpression $expression
     * @param StringCompiler $compiler
     * @return string
     */
    public function treeExpression(TreeExpression $expression, StringCompiler $compiler)
    {
        $op = strtoupper($expression->getValue());
        $pieces = array();

        foreach ($expression->getChildren() as $child) {
            $piece = $this->wrapCompositeExpression($child, $compiler->compile($child));

            $pieces[] = $piece;
        }

        return implode(" $op ", $pieces);
    }

    /**
     * @param BinaryExpression $expression
     * @param StringCompiler $compiler
     * @return string
     */
    public function binaryExpression(BinaryExpression $expression, StringCompiler $compiler)
    {
        $op = strtoupper($expression->getValue());
        return "{$compiler->compile($expression->getLeft(), $compiler)} $op {$compiler->compile($expression->getRight(), $compiler)}";
    }

    /**
     * @param BinaryExpression $expression
     * @param StringCompiler $compiler
     * @return string
     */
    public function binaryExpressionWithNoSpaces(BinaryExpression $expression, StringCompiler $compiler)
    {
        $op = strtoupper($expression->getValue());
        return "{$compiler->compile($expression->getLeft(), $compiler)}$op{$compiler->compile($expression->getRight(), $compiler)}";
    }

    /**
     * @param BasicExpression $expression
     * @param StringCompiler $compiler
     * @return string
     */
    public function basicExpression(BasicExpression $expression, StringCompiler $compiler)
    {
        $value = $this->phraseExpression($expression->getValue());

        return $value;
    }

    /**
     * @param $expression
     * @param $string
     * @return string
     */
    private function wrapCompositeExpression($expression, $string)
    {
        if ($expression instanceof TreeExpression)
            $string = "($string)";

        return $string;
    }

    /**
     * @param $string
     * @return string
     */
    private function phraseExpression($string)
    {
        if (strstr($string, ' ') !== false)
            $string = '"' . $string . '"';

        return $string;
    }
} 