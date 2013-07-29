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
use DSQ\Expression\Expression;
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
    private $opsWeights = array();

    public function __construct()
    {
        $this
            ->registerTransformation(array($this, 'basicExpression'), '*', '*')
            ->registerTransformation(array($this, 'binaryExpression'), 'DSQ\Expression\BinaryExpression')
            ->registerTransformation(array($this, 'treeExpression'), 'DSQ\Expression\TreeExpression')
            ->registerTransformation(array($this, 'binaryExpressionWithNoSpaces'), 'DSQ\Expression\BinaryExpression', '^')

            ->setOpWeight('^', 1100)
            ->setOpWeight('_', 1100)
            ->setOpWeight('*', 1000)
            ->setOpWeight('/', 1000)
            ->setOpWeight('%', 1000)
            ->setOpWeight('+', 900)
            ->setOpWeight('-', 900)
            ->setOpWeight('=', 800)
            ->setOpWeight('!=', 800)
            ->setOpWeight('<=', 800)
            ->setOpWeight('>=', 800)
            ->setOpWeight('<', 800)
            ->setOpWeight('>', 800)
            ->setOpWeight('and', 700)
            ->setOpWeight('or', 700)
            ->setOpWeight('xor', 700)
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
            $piece = $this->precedenceParenthesis($expression, $child, $compiler->compile($child));

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
     * Set operator weight for managing precedences
     *
     * @param string $op
     * @param int $weight
     *
     * @return $this The current instance
     */
    public function setOpWeight($op, $weight)
    {
        $this->opsWeights[$op] = (int) $weight;

        return $this;
    }

    /**
     * Get operator weight
     *
     * @param $op
     * @return int
     */
    public function getOpWeight($op)
    {
        if (isset($this->opsWeights[$op]))
            return $this->opsWeights[$op];

        return 0;
    }

    /**
     * @param $expression
     * @param $string
     * @return string
     */
    private function precedenceParenthesis(Expression $parent, Expression $child, $string)
    {
        if (!$this->isAtomic($child) && $this->getOpWeight($parent->getValue()) >= $this->getOpWeight($child->getValue()))
            $string = "($string)";

        return $string;
    }

    private function isAtomic(Expression $expression)
    {
        return $expression instanceof BasicExpression && $expression->getType() == 'basic';
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