<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Language;


use Dissect\Parser\LALR1\Parser;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\Builder\ExpressionBuilder;
use DSQ\Expression\FieldExpression;
use DSQ\Expression\TreeExpression;
use DSQ\Language\Grammar;
use DSQ\Language\Lexer;

class GrammarTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Parser */
    protected $parser;

    /** @var  ExpressionBuilder */
    protected $builder;

    /** @var  ExpressionBuilder */
    protected $orBuilder;

    /** @var  Lexer */
    protected $lexer;

    public function setUp()
    {
        $this->lexer = new Lexer;
        $this->parser = new Parser(new Grammar);
        $this->builder = new ExpressionBuilder;
        $this->orBuilder = new ExpressionBuilder('OR');
    }

    public function testFieldExpression()
    {
        $this->assertEquals(
            new FieldExpression('foo', 'bar, baz', '='),
            $this->parse("foo = bar\\,\\ baz")
        );

        $this->assertEquals(
            new FieldExpression('foo', 'bar baz bag', '='),
            $this->parse("foo = (bar baz bag)")
        );

        $this->assertEquals(
            new FieldExpression('foo', '"quoted\\"escaped()"', '='),
            $this->parse("foo = \"quoted\\\"escaped()\"")
        );
    }

    public function testFieldExpressionWithCompositeValues()
    {
        $this->assertEquals(
            new FieldExpression('foo', array('a' => array('b' => 'c', 'd' => 'e')), '='),
            $this->parse("foo = (a = (b = c, d = e))")
        );
    }

    public function testINExpressions()
    {
        $expr = $this->orBuilder
            ->field('foo', 'a')
            ->field('foo', 'b c')
            ->field('foo', '"d"')
            ->field('foo', array('f' => 'v', 'ff' => 'vv'))
            ->get()
        ;

        $this->assertEquals(
            $expr,
            $this->parse('foo IN  (a, (b c), "d", (f = v, ff = vv))')
        );
    }

    public function testNotINExpressions()
    {
        $expr = $this->orBuilder
            ->field('foo', 'a')
            ->field('foo', 'b c')
            ->field('foo', '"d"')
            ->field('foo', array('f' => 'v', 'ff' => 'vv'))
            ->get()
        ;

        $not = new TreeExpression('NOT');
        $not->addChild($expr);

        $this->assertEquals(
            $not,
            $this->parse('foo NOT IN  (a, (b c), "d", (f = v, ff = vv))')
        );
    }

    public function testCompositeExpressions()
    {
        $this->assertEquals(
            new FieldExpression('foo', array('a' => 'b', 'c' => array('d' => '"e"', 'f' => 'hi all()')), '='),
            $this->parse('foo = (a = b, c = (d = "e", f = (hi all\(\))))')
        );
    }

    public function testNotEqual()
    {
        $this->assertEquals(
            new FieldExpression('foo', 'bar', '!='),
            $this->parse('foo != bar')
        );

        $this->assertEquals(
            new FieldExpression('foo', '2000', '>='),
            $this->parse('foo >= 2000')
        );

        $this->assertEquals(
            new FieldExpression('foo', '2000', '<'),
            $this->parse('foo < 2000')
        );

        $this->assertEquals(
            new FieldExpression('foo', '2000', '<='),
            $this->parse('foo <= 2000')
        );
    }

    public function testComparisons()
    {
        $this->assertEquals(
            new FieldExpression('foo', '2000', '>'),
            $this->parse('foo > 2000')
        );

        $this->assertEquals(
            new FieldExpression('foo', '2000', '>='),
            $this->parse('foo >= 2000')
        );

        $this->assertEquals(
            new FieldExpression('foo', '2000', '<'),
            $this->parse('foo < 2000')
        );

        $this->assertEquals(
            new FieldExpression('foo', '2000', '<='),
            $this->parse('foo <= 2000')
        );
    }

    private function parse($dsl)
    {
        return $this->parser->parse($this->lexer->lex($dsl));
    }
}
 