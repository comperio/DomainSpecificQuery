<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Compiler\LuceneCompiler;


use DSQ\Compiler\TypeBasedCompiler;
use DSQ\Expression\BasicExpression;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\TreeExpression;
use DSQ\Lucene\BasicLuceneExpression;
use DSQ\Lucene\BooleanExpression;
use DSQ\Lucene\FieldExpression;
use DSQ\Lucene\RangeExpression;

class LuceneCompiler extends TypeBasedCompiler
{
    public function __construct()
    {
        $this
            ->registerTransformation(array($this, 'basicExpression'), '*', '*')
            ->registerTransformation(array($this, 'fieldExpression'), '*', '=')
            ->registerTransformation(array($this, 'treeExpression'), 'DSQ\Expression\TreeExpression')
            ->registerTransformation(array($this, 'comparisonExpression'), 'DSQ\Expression\BinaryExpression')

        ;
    }

    public function basicExpression(BasicExpression $expr, self $compiler)
    {
        return new BasicLuceneExpression($expr->getValue());
    }

    public function fieldExpression(BinaryExpression $expr, self $compiler)
    {
        $value = $compiler->transform($expr->getRight()->getValue());

        return new FieldExpression((string) $expr->getLeft()->getValue(), $value);
    }

    public function treeExpression(TreeExpression $expr, self $compiler)
    {
        switch ($expr->getValue()) {
            case 'and':
                $operator = BooleanExpression::MUST;
                break;
            case 'not':
                $operator = BooleanExpression::MUST_NOT;
                break;
            default:
                $operator = BooleanExpression::SHOULD;
        }

        $booleanExpr = new BooleanExpression($operator);

        foreach ($expr->getChildren() as $child)
        {
            $booleanExpr->addExpression($compiler->compile($child));
        }

        return $booleanExpr;
    }

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
} 