<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Lucene\Compiler;


use DSQ\Lucene\BooleanExpression;
use DSQ\Lucene\Compiler\LuceneQueryCompiler;
use DSQ\Lucene\PureExpression;
use DSQ\Lucene\SpanExpression;

class LuceneQueryCompilerTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultMap()
    {
        $compiler = new LuceneQueryCompiler();
        $expr = new PureExpression('');

        $query = $compiler->compile($expr);
        $this->assertEquals($expr, $query->getMainQuery());

        $expr['filter'] = true;
        $query = $compiler->compile($expr);
        $this->assertEquals(array($expr), $query->getFilterQueries());
    }

    public function testAndMap()
    {
        $compiler = new LuceneQueryCompiler();
        $expr = new SpanExpression('AND');

        $expr
            ->addExpression($subexp1 = new PureExpression('1'))
            ->addExpression($subexp2 = new PureExpression('2'))
            ->addExpression($subexp3 = new PureExpression('3'))
        ;

        $subexp2['filter'] = true;

        $query = $compiler->compile($expr);
        $this->assertEquals('(1) AND (3)', (string) $query->getMainQuery());
        $this->assertEquals(array($subexp2), $query->getFilterQueries());
    }

    public function testOrMap()
    {
        $compiler = new LuceneQueryCompiler();
        $expr = new SpanExpression('OR');

        $expr
            ->addExpression($subexp1 = new PureExpression('1'))
            ->addExpression($subexp2 = new PureExpression('2'))
            ->addExpression($subexp3 = new PureExpression('3'))
        ;

        $subexp2['filter'] = true;

        $query = $compiler->compile($expr);
        $this->assertEquals('(1) OR (2) OR (3)', (string) $query->getMainQuery());

        $query = $compiler->compile($expr);
        $this->assertEquals(array(), $query->getFilterQueries());
    }

    public function testNotMap()
    {
        $compiler = new LuceneQueryCompiler();
        $expr = new BooleanExpression(BooleanExpression::MUST_NOT);

        $expr
            ->addExpression($subexp1 = new PureExpression('1'))
            ->addExpression($subexp2 = new PureExpression('2'))
            ->addExpression($subexp3 = new PureExpression('3'))
            ->addExpression($subexp4 = new PureExpression('4'))
        ;

        $subexp2['filter'] = $subexp4['filter'] = true;

        $query = $compiler->compile($expr);
        $this->assertEquals('-(1) -(3)', (string) $query->getMainQuery());

        $filters = $compiler->compile($expr)->getFilterQueries();

        $this->assertEquals('-(2)', (string) $filters[0]);
        $this->assertEquals('-(4)', (string) $filters[1]);
    }
}
 