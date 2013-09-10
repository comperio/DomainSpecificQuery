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


use DSQ\Expression\BasicExpression;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\FieldExpression;
use DSQ\Lucene\Compiler\LuceneCompiler;
use DSQ\Lucene\Compiler\Map\MapBuilder;
use DSQ\Lucene\PureExpression;
use DSQ\Lucene\TermExpression;
use DSQ\Compiler\UnregisteredTransformationException;

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

        $expr = new FieldExpression('foo', 'bar');

        $this->assertEquals("bar", (string) $map($expr, $this->compiler));

        $expr->setValue('esca"pe');
        $map = $this->builder->term(true, 2);

        $this->assertEquals('"esca\"pe"^2', (string) $map($expr, $this->compiler));
    }

    public function testField()
    {
        $map = $this->builder->field('foo');

        $expr = new FieldExpression('moo', 'bar');
        $this->assertEquals("foo:bar", (string) $map($expr, $this->compiler));

        $expr->setValue('esca"pe');
        $map = $this->builder->field('foo', true, 2);

        $this->assertEquals('foo:"esca\"pe"^2', (string) $map($expr, $this->compiler));
    }

    public function testSpan()
    {
        $map = $this->builder->span(array('foo', 'goo', 'moo'), 'or');

        $expr = new FieldExpression('moo', 'bar');
        $this->assertEquals("foo:bar OR goo:bar OR moo:bar", (string) $map($expr, $this->compiler));

        $map = $this->builder->span(array('foo', 'goo', 'moo'), 'or', true, 3);
        $this->assertEquals('(foo:"bar" OR goo:"bar" OR moo:"bar")^3', (string) $map($expr, $this->compiler));
    }

    public function testRange()
    {
        $map = $this->builder->range('foo');

        $expr = new FieldExpression('moo', array('from' => 1, 'to' => 2));
        $this->assertEquals("foo:[1 TO 2]", (string) $map($expr, $this->compiler));

        $map = $this->builder->range('foo', 3.1);
        $this->assertEquals("foo:[1 TO 2]^3.1", (string) $map($expr, $this->compiler));
    }

    public function testRangeWithScalarValue()
    {
        $map = $this->builder->range('foo');

        $expr = new FieldExpression('moo', '2012');
        $this->assertEquals("foo:2012", (string) $map($expr, $this->compiler));
    }

    public function testTemplate()
    {
        $map = $this->builder->template('foo:{}');

        $expr = new FieldExpression('moo', 'a:b:c');
        $this->assertEquals("foo:a\:b\:c", (string) $map($expr, $this->compiler));

        $map = $this->builder->template('foo:{}', true, true, 3);
        $this->assertEquals("(foo:\"a:b:c\")^3", (string) $map($expr, $this->compiler));
    }

    public function testTemplateWithNestedValues()
    {
        $map = $this->builder->template('foo:{subvalue}');

        $expr = new FieldExpression('moo', array('subvalue' => 'bar'));
        $this->assertEquals("foo:bar", (string) $map($expr, $this->compiler));
    }

    public function testCombine()
    {
        $map = $this->builder->combine(
            'or',
            function ($expr) {return new TermExpression("foo");},
            function ($expr) {return new TermExpression("bar");},
            function ($expr) {return new TermExpression("baz");}
        );

        $expr = new FieldExpression('moo', 'bar');

        $this->assertEquals("foo OR bar OR baz", (string) $map($expr, $this->compiler));
    }

    public function testRegexps()
    {
        $map = $this->builder->regexps(array(
            '/bar/' => function ($expr) {return new TermExpression("barbar");},
            '/.+/' => function ($expr) {return new TermExpression("ohoh");},
        ));

        $expr = new FieldExpression('moo', 'bar');
        $this->assertEquals("barbar", (string) $map($expr, $this->compiler));

        $expr->setValue("baz");
        $this->assertEquals("ohoh", (string) $map($expr, $this->compiler));

        $expr->setValue("");
        $this->setExpectedException('DSQ\Compiler\UnregisteredTransformationException');

        $map($expr, $this->compiler);
    }

    public function testConditional()
    {
        $map = $this->builder->conditional(
            function($expr) { return strstr($expr->getValue(), 'b') !== false; },
            function ($expr, $c) {return new TermExpression("it has a b");},

            function($expr) { return true; },
            function ($expr, $c) {return new TermExpression("it does not have a b");}
        );

        $expr = new FieldExpression('moo', 'bar');
        $this->assertEquals("it has a b", (string) $map($expr, $this->compiler));

        $expr->setValue("mar");
        $this->assertEquals("it does not have a b", (string) $map($expr, $this->compiler));

        $map = $this->builder->conditional(
            function($expr) { return false; },
            function ($expr, $c) {return 'will never run'; }
        );

        $this->setExpectedException('DSQ\Compiler\UnregisteredTransformationException');
        $map($expr, $this->compiler);
    }

    public function testAttr()
    {
        $submap = function($expr) { return new PureExpression('foo'); };
        $map = $this->builder->attr($submap, array('foo' => 'bar', 'moo' => 'bah'));
        $expr = $map(new BasicExpression('bar'), $this->compiler);

        $this->assertInstanceOf('DSQ\Lucene\PureExpression', $expr);
        $this->assertEquals('foo', $expr->getValue());
        $this->assertTrue(isset($expr['foo']));
        $this->assertTrue(isset($expr['moo']));
        $this->assertFalse(isset($expr['bar']));
        $this->assertEquals('bar', $expr['foo']);
        $this->assertEquals('bah', $expr['moo']);
    }

    public function testSubval()
    {
        $rval = function($expr) { return $expr->getValue(); };

        $expr = new FieldExpression('moo', 'bar');
        $map = $this->builder->subval($rval, 'key');
        $this->assertEquals('bar', $map($expr, $this->compiler));

        $expr->setValue(array('key' => 'foo'));
        $this->assertEquals('foo', $map($expr, $this->compiler));

        $expr->setValue(array('nokey' => 'foo'));
        $this->assertEquals('', $map($expr, $this->compiler));
    }

    public function testHasSubval()
    {
        $condition = $this->builder->hasSubval('key');
        $expr = new FieldExpression('foo', 'bar');

        $this->assertFalse($condition($expr));

        $expr->setValue(array('a' => 'b'));
        $this->assertFalse($condition($expr));

        $expr->setValue(array('key' => ''));
        $this->assertFalse($condition($expr));

        $expr->setValue(array('key' => 'b'));
        $this->assertTrue($condition($expr));

        $condition = $this->builder->hasSubval('key', false);
        $expr->setValue(array('key' => ''));
        $this->assertTrue($condition($expr));
    }

    public function testConstant()
    {
        $cond = $this->builder->constant("abc");

        $this->assertEquals("abc", $cond());
    }


}
 