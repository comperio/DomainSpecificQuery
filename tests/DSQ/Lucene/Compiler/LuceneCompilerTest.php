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


use DSQ\Expression\BasicExpression;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\FieldExpression;
use DSQ\Expression\TreeExpression;
use DSQ\Lucene\BooleanExpression;
use DSQ\Lucene\Compiler\LuceneCompiler;
use DSQ\Lucene\FieldExpression as LuceneFieldExpression;
use DSQ\Lucene\PhraseExpression;
use DSQ\Lucene\RangeExpression;
use DSQ\Lucene\SpanExpression;
use DSQ\Lucene\TermExpression;

class LuceneCompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var  LuceneCompiler */
    protected $compiler;

    public function setUp()
    {
        $this->compiler = new LuceneCompiler;
        $this->compiler->map('foo', function (FieldExpression $f) {
            return new LuceneFieldExpression('foo', $f->getValue());
        });
    }

    public function testCompileBasicExpression()
    {
        $expr = new BasicExpression('hey', 'man');
        $compiled = $this->compiler->compile($expr);

        $this->assertEquals(new TermExpression('hey'), $compiled);
    }

    public function testCompileFieldExpression()
    {
        $expr = new FieldExpression('foo', 'bar');
        $this->compiler->mapByClass('DSQ\Expression\FieldExpression', array($this->compiler, 'fieldExpression'));
        $compiled = $this->compiler->compile($expr);

        $this->assertEquals(new LuceneFieldExpression('foo', 'bar'), $compiled);
    }

    public function testRangeExpression()
    {
        $expr = new BinaryExpression('range', 'foo', 'bar');
        $compiled = $this->compiler->compile($expr);

        $this->assertEquals(new RangeExpression('foo', 'bar'), $compiled);
    }

    public function testCompileTreeExpression()
    {
        $expr = new TreeExpression('and');
        $expr->setChildren(array(new FieldExpression('foo', 'val'), 'b', 'c'));
        $compiled = $this->compiler->compile($expr);

        $this->assertEquals(new SpanExpression('AND', array(new LuceneFieldExpression('foo', new TermExpression('val')), 'b', 'c')), $compiled);

        $expr->setType('or')->setValue('or');
        $compiled = $this->compiler->compile($expr);
        $this->assertEquals(new SpanExpression('OR', array(new LuceneFieldExpression('foo', new TermExpression('val')), 'b', 'c')), $compiled);
    }

    public function testCompileTreeExpressionIsCaseInsensitive()
    {
        $expr = new TreeExpression('AnD');
        $expr->setChildren(array(new FieldExpression('foo', 'val'), 'b', 'c'));
        $compiled = $this->compiler->compile($expr);

        $this->assertEquals(new SpanExpression('AND', array(new LuceneFieldExpression('foo', new TermExpression('val')), 'b', 'c')), $compiled);
    }

    public function testCompileNotExpression()
    {
        $expr = new TreeExpression('not');
        $expr->setChildren(array('a', 'b', 'c'));
        $compiled = $this->compiler->compile($expr);

        $this->assertEquals(
            new BooleanExpression(BooleanExpression::MUST_NOT, array('a', 'b', 'c')),
            $compiled
        );
    }

    public function testCompileComparisonExpression()
    {
        $expr = new BinaryExpression('>', 'a', 12);
        $compiled = $this->compiler->compile($expr);

        $this->assertEquals(
            new LuceneFieldExpression('a', new RangeExpression(12, '*', 1.0, false)),
            $compiled
        );

        $expr = new BinaryExpression('>=', 'a', 12);
        $compiled = $this->compiler->compile($expr);

        $this->assertEquals(
            new LuceneFieldExpression('a', new RangeExpression(12, '*')),
            $compiled
        );

        $expr = new BinaryExpression('<', 'a', 12);
        $compiled = $this->compiler->compile($expr);

        $this->assertEquals(
            new LuceneFieldExpression('a', new RangeExpression('*', 12, 1.0, true, false)),
            $compiled
        );

        $expr = new BinaryExpression('<=', 'a', 12);
        $compiled = $this->compiler->compile($expr);

        $this->assertEquals(
            new LuceneFieldExpression('a', new RangeExpression('*', 12)),
            $compiled
        );
    }

    public function testPhrasize()
    {
        $this->assertEquals('foo', $this->compiler->phrasize('foo', false));
        $this->assertEquals(new PhraseExpression('foo'), $this->compiler->phrasize('foo', true));
    }

    public function testPhrasizeWithArrayValues()
    {
        $ary = array('a', 'b' => array('c'));
        $expected = array (new PhraseExpression('a'), 'b' => array(new PhraseExpression('c')));
        $this->assertEquals($expected, $this->compiler->phrasize($ary));
    }

    public function testPhrasizeOrTermize()
    {
        $this->assertEquals('foo', $this->compiler->phrasizeOrTermize('foo', false, false));
        $this->assertEquals(new PhraseExpression('foo'), $this->compiler->phrasizeOrTermize('foo', true, true));
        $this->assertEquals(new PhraseExpression('foo'), $this->compiler->phrasizeOrTermize('foo', true, false));
        $this->assertEquals(new TermExpression('foo'), $this->compiler->phrasizeOrTermize('foo', false, true));
    }

    public function testPhrasizeOrTermizeWithArrayValues()
    {
        $ary = array('a', 'b' => array('c'));
        $expected = array (new TermExpression('a'), 'b' => array(new TermExpression('c')));
        $this->assertEquals($expected, $this->compiler->phrasizeOrTermize($ary, false, true));
    }
}
 