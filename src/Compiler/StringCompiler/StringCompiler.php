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


use DSQ\Compiler\MatcherCompiler;
use DSQ\Expression\BasicExpression;
use DSQ\Expression\FieldExpression;
use DSQ\Expression\UnaryExpression;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\Expression;
use DSQ\Expression\TreeExpression;

/**
 * Class StringCompiler
 *
 * This is a simple compiler that converts expression using a math-style infix notation.
 * It's a simple example to show how one can deal with operator precedence.
 *
 * @package DSQ\Compiler\StringCompiler
 */
class StringCompiler extends MatcherCompiler
{
    private $opsWeights = array();

    public function __construct()
    {
        parent::__construct();

        $matcher = $this->getMatcher();
        $matcher->setDefault(array($this, 'basicExpression'));

        $this
            ->mapByClass('DSQ\Expression\UnaryExpression', array($this, 'unaryExpression'))
            ->mapByClass('DSQ\Expression\BinaryExpression', array($this, 'binaryExpression'))
            ->mapByClass('DSQ\Expression\TreeExpression', array($this, 'treeExpression'))
            ->mapByClass('DSQ\Expression\FieldExpression', array($this, 'fieldExpression'))
            ->map('^', array($this, 'binaryExpressionWithNoSpaces'))

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
     * @param FieldExpression $expression
     * @param StringCompiler $compiler
     * @return string
     */
    public function fieldExpression(FieldExpression $expression, StringCompiler $compiler)
    {
        return "{$expression->getField()}: {$this->phraseExpression($expression->getValue())}";
    }

    /**
     * @param BinaryExpression $expression
     * @param StringCompiler $compiler
     * @return string
     */
    public function binaryExpression(BinaryExpression $expression, StringCompiler $compiler)
    {
        $op = strtoupper($expression->getValue());

        return sprintf("%s %s %s",
            $this->precedenceParenthesis($expression, $expression->getLeft(), $compiler->compile($expression->getLeft())),
            $op,
            $this->precedenceParenthesis($expression, $expression->getRight(), $compiler->compile($expression->getRight()))
        );
    }

    /**
     * @param BinaryExpression $expression
     * @param StringCompiler $compiler
     * @return string
     */
    public function binaryExpressionWithNoSpaces(BinaryExpression $expression, StringCompiler $compiler)
    {
        $op = strtoupper($expression->getValue());

        return sprintf("%s%s%s",
            $this->precedenceParenthesis($expression, $expression->getLeft(), $compiler->compile($expression->getLeft())),
            $op,
            $this->precedenceParenthesis($expression, $expression->getRight(), $compiler->compile($expression->getRight()))
        );
    }

    /**
     * @param UnaryExpression $expression
     * @param StringCompiler $compiler
     * @return string
     */
    public function unaryExpression(UnaryExpression $expression, StringCompiler $compiler)
    {
        $op = strtoupper($expression->getValue());

        return sprintf("%s%s",
            $op,
            $this->precedenceParenthesis($expression, $expression->getChild(), $compiler->compile($expression->getChild()))
        );
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

    /**
     * @param Expression $expression
     * @return bool
     */
    private function isAtomic(Expression $expression)
    {
        return
            ($expression instanceof BasicExpression && $expression->getType() == 'basic')
            || $expression instanceof FieldExpression
            || ($expression instanceof TreeExpression && $expression->count() == 1);
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