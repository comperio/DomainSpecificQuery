<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Compiler\Map;


use DSQ\Expression\BinaryExpression;
use DSQ\Lucene\Compiler\LuceneCompiler;
use DSQ\Lucene\Compiler\Map\MapBuilder;
use DSQ\Lucene\TermExpression;

class MapBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LuceneCompiler
     */
    protected $compiler;

    /**
     * @var MapBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->compiler = new LuceneCompiler;
        $this->builder = new MapBuilder;
    }

    public function testTerm()
    {
        $map = $this->builder->term();

        $expr = new BinaryExpression('=', 'foo', 'bar');

        $this->assertEquals("bar", (string) $map($expr, $this->compiler));

        $expr->setRight('esca"pe');
        $map = $this->builder->term(true, 2);

        $this->assertEquals('"esca\"pe"^2', (string) $map($expr, $this->compiler));
    }

    public function testField()
    {
        $map = $this->builder->field('foo');

        $expr = new BinaryExpression('=', 'moo', 'bar');
        $this->assertEquals("foo:bar", (string) $map($expr, $this->compiler));

        $expr->setRight('esca"pe');
        $map = $this->builder->field('foo', true, 2);

        $this->assertEquals('foo:"esca\"pe"^2', (string) $map($expr, $this->compiler));
    }

    public function testSpan()
    {
        $map = $this->builder->span(array('foo', 'goo', 'moo'), 'or');

        $expr = new BinaryExpression('=', 'moo', 'bar');
        $this->assertEquals("foo:bar OR goo:bar OR moo:bar", (string) $map($expr, $this->compiler));

        $map = $this->builder->span(array('foo', 'goo', 'moo'), 'or', true, 3);
        $this->assertEquals('(foo:"bar" OR goo:"bar" OR moo:"bar")^3', (string) $map($expr, $this->compiler));
    }

    public function testRange()
    {
        $map = $this->builder->range('foo');

        $expr = new BinaryExpression('=', 'moo', array('from' => 1, 'to' => 2));
        $this->assertEquals("foo:[1 TO 2]", (string) $map($expr, $this->compiler));

        $map = $this->builder->range('foo', 3.1);
        $this->assertEquals("foo:[1 TO 2]^3.1", (string) $map($expr, $this->compiler));
    }

    public function testTemplate()
    {
        $map = $this->builder->template('foo:{}');

        $expr = new BinaryExpression('=', 'moo', 'a:b:c');
        $this->assertEquals("foo:a\:b\:c", (string) $map($expr, $this->compiler));

        $map = $this->builder->template('foo:{}', true, true, 3);
        $this->assertEquals("(foo:\"a:b:c\")^3", (string) $map($expr, $this->compiler));
    }

    public function testCombine()
    {
        $map = $this->builder->combine(
            'or',
            function ($expr) {return new TermExpression("foo");},
            function ($expr) {return new TermExpression("bar");},
            function ($expr) {return new TermExpression("baz");}
        );

        $expr = new BinaryExpression('=', 'moo', 'bar');

        $this->assertEquals("foo OR bar OR baz", (string) $map($expr, $this->compiler));
    }
}
 