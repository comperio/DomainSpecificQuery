<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Lucene\Compiler;


use DSQ\Compiler\TypeBasedCompiler;
use DSQ\Expression\BasicExpression;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\TreeExpression;
use DSQ\Expression\FieldExpression;

use DSQ\Lucene\PhraseExpression;
use DSQ\Lucene\SpanExpression;
use DSQ\Lucene\TermExpression;
use DSQ\Lucene\BooleanExpression;
use DSQ\Lucene\RangeExpression;
use DSQ\Lucene\FieldExpression as LuceneFieldExpression;

/**
 * Class LuceneCompiler
 *
 * Compile an expression into a Lucene Expression.
 *
 * @package DSQ\Lucene\Compiler
 */
class LuceneCompiler extends TypeBasedCompiler
{
    public function __construct()
    {
        $this
            ->map('*', array($this, 'basicExpression'))
            ->map('*:DSQ\Expression\FieldExpression', array($this, 'fieldExpression'))
            ->map(array('and:DSQ\Expression\TreeExpression', 'or:DSQ\Expression\TreeExpression'), array($this, 'treeExpression'))
            ->map('not:DSQ\Expression\TreeExpression', array($this, 'notExpression'))
            ->map('range:DSQ\Expression\BinaryExpression', array($this, 'rangeExpression'))
            ->map(array('>', '>=', '<', '<='), array($this, 'comparisonExpression'))
        ;
    }

    /**
     * @param BasicExpression $expr
     * @param LuceneCompiler $compiler
     * @return TermExpression
     */
    public function basicExpression(BasicExpression $expr, self $compiler)
    {
        return new TermExpression($expr->getValue());
    }

    /**
     * @param FieldExpression $expr
     * @param LuceneCompiler $compiler
     * @return FieldExpression
     */
    public function fieldExpression(FieldExpression $expr, self $compiler)
    {
        $value = $compiler->transform($expr->getValue());

        return new LuceneFieldExpression($expr->getField(), $expr->getValue(), $value);
    }

    /**
     * @param BinaryExpression $expr
     * @param LuceneCompiler $compiler
     * @return RangeExpression
     */
    public function rangeExpression(BinaryExpression $expr, self $compiler)
    {
        $from = $compiler->transform($expr->getLeft());
        $to = $compiler->transform($expr->getRight());

        return new RangeExpression($from, $to);
    }

    /**
     * @param TreeExpression $expr
     * @param LuceneCompiler $compiler
     * @return SpanExpression
     */
    public function treeExpression(TreeExpression $expr, self $compiler)
    {
        switch (strtolower($expr->getValue())) {
            case 'and':
                $operator = SpanExpression::OP_AND;
                break;
            default:
                $operator = SpanExpression::OP_OR;
        }

        $spanExpr = new SpanExpression($operator, $compiler->compileArray($expr->getChildren()));

        return $spanExpr;
    }

    /**
     * @param TreeExpression $expr
     * @param LuceneCompiler $compiler
     * @return BooleanExpression
     */
    public function notExpression(TreeExpression $expr, self $compiler)
    {
        return new BooleanExpression(BooleanExpression::MUST_NOT, $compiler->compileArray($expr->getChildren()));
    }

    /**
     * @param BinaryExpression $expr
     * @param LuceneCompiler $compiler
     * @return FieldExpression
     */
    public function comparisonExpression(BinaryExpression $expr, self $compiler)
    {
        $fieldname = $expr->getLeft()->getValue();
        $val = $compiler->transform($expr->getRight()->getValue());

        $from = '*';
        $to = '*';
        $includeLeft = true;
        $includeRight = true;

        switch ($expr->getValue())
        {
            case '>':
                $from = $val;
                $includeLeft = false;
                break;
            case '>=':
                $from = $val;
                break;
            case '<':
                $to = $val;
                $includeRight = false;
                break;
            case '<=':
                $to = $val;
                break;
        }

        return new FieldExpression($fieldname, new RangeExpression($from, $to, 1.0, $includeLeft, $includeRight));
    }

    /**
     * Helper function that wraps an expression value with a phrase expression if $phrase = true.
     *
     * @param mixed $value
     * @param bool $phrase
     * @return PhraseExpression|string
     */
    public function phrasize($value, $phrase = true)
    {
        if (!$phrase)
            return $value;

        if (!is_array($value))
            return new PhraseExpression($value);

        $ary = array();
        foreach ($value as $key => $v) {
            $ary[$key] = $this->phrasize($v, $phrase);
        }

        return $ary;
    }

    /**
     * Helper function that wraps an expression value with a phrase expression or a term expression
     * Phrasing wins on termizing
     *
     * @param mixed $value
     * @param bool $phrase
     * @param bool $escape
     * @return PhraseExpression|TermExpression|string
     */
    public function phrasizeOrTermize($value, $phrase = true, $escape = true)
    {
        if (!$phrase && !$escape)
            return $value;

        if (!is_array($value))
            return $phrase
                ? new PhraseExpression($value)
                : new TermExpression($value)
            ;

        $ary = array();
        foreach ($value as $key => $v) {
            $ary[$key] = $this->phrasizeOrTermize($v, $phrase, $escape);
        }

        return $ary;
    }
} 