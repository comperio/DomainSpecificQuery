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
use DSQ\Lucene\BooleanExpression;
use DSQ\Lucene\LuceneExpression;
use DSQ\Lucene\LuceneQuery;
use DSQ\Lucene\SpanExpression;
use DSQ\Lucene\TreeExpression;

class LuceneQueryCompiler extends TypeBasedCompiler
{
    /**
     * Register maps
     */
    public function __construct()
    {
        $this
            ->map('*', array($this, 'mapExpression'))
            ->map(array('AND', '+'), array($this, 'mapAnd'))
            ->map('-', array($this, 'mapNot'))
        ;
    }

    /**
     * @param LuceneExpression $expr
     * @param LuceneQueryCompiler $compiler
     * @return LuceneQuery
     */
    public function mapExpression(LuceneExpression $expr, LuceneQueryCompiler $compiler)
    {
        $query = $this->newQuery();

        if ($expr->attr('filter', false))
            $query->addFilterQuery($expr);
        else
            $query->setMainQuery($expr);

        return $query;
    }

    /**
     * @param TreeExpression $expr
     * @param LuceneQueryCompiler $compiler
     * @return LuceneQuery
     */
    public function mapAnd(TreeExpression $expr, LuceneQueryCompiler $compiler)
    {
        $query = $this->newQuery();
        $and = $expr instanceof SpanExpression
            ? new SpanExpression('AND') : new BooleanExpression(BooleanExpression::MUST);

        foreach ($expr->getExpressions() as $child) {
            $subQuery = $compiler->compile($child);
            $this->mergeFilterQueries($query, $subQuery);
            if (!$subQuery->hasTrivialMainQuery()){
                $and->addExpression($subQuery->getMainQuery());
            }
        }

        if ($and->numOfExpressions())
            $query->setMainQuery($and);

        return $query;
    }

    /**
     * @param BooleanExpression $expr
     * @param LuceneQueryCompiler $compiler
     * @return LuceneQuery
     */
    public function mapNot(BooleanExpression $expr, LuceneQueryCompiler $compiler)
    {
        $query = $this->newQuery();
        $not = new BooleanExpression(BooleanExpression::MUST_NOT);

        foreach ($expr->getExpressions() as $child) {
            $subQuery = $compiler->compile($child);

            if ($this->hasQueryOnlyOneFilter($subQuery)) {
                $query->addFilterQuery(new BooleanExpression(
                    BooleanExpression::MUST_NOT, $subQuery->getFilterQueries()
                ));
            } else {
                $not->addExpression($this->queryToAndExpression($subQuery));
            }
        }

        if ($not->numOfExpressions())
            $query->setMainQuery($not);

        return $query;
    }

    /**
     * @return LuceneQuery
     */
    protected function newQuery()
    {
        return new LuceneQuery;
    }

    /**
     * @param LuceneQuery $query1
     * @param LuceneQuery $query2
     * @return $this
     */
    private function mergeFilterQueries(LuceneQuery $query1, LuceneQuery $query2)
    {
        $query1->addFilterQueries($query2->getFilterQueries());
        return $this;
    }

    /**
     * Convert a query to an AND expression
     *
     * @param LuceneQuery $query
     * @return SpanExpression
     */
    private function queryToAndExpression(LuceneQuery $query)
    {
        $span = new SpanExpression('AND');

        if (!$query->hasTrivialMainQuery())
            $span->addExpression($query->getMainQuery());

        $span->addExpressions($query->getFilterQueries());

        return $span;
    }

    /**
     * Tells us id the query has exactly one filter and a trivial main query
     *
     * @param LuceneQuery $query
     *
     * @return bool
     */
    private function hasQueryOnlyOneFilter(LuceneQuery $query)
    {
        return
            $query->hasTrivialMainQuery()
            && count($query->getFilterQueries()) == 1
        ;
    }
} 