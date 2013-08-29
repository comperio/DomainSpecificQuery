<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

use DSQ\Comperio\Compiler\Map\SubjTypeMap;
use DSQ\Expression\FieldExpression;
use DSQ\Lucene\Compiler\LuceneCompiler;

class SubjTypeMapTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SubjTypeMap
     */
    private $map;

    /**
     * @var LuceneCompiler
     */
    private $compiler;

    protected function setUp()
    {
        $this->map = new SubjTypeMap(null, null, array(600, 601));
        $this->compiler = new LuceneCompiler;
    }

    public function testOnlySubject()
    {
        $map = $this->map;

        $expr = new FieldExpression('foo', array('s' => 'bar:bar'));
        $this->assertEquals('fldin_txt_subject:(bar\:bar)', (string) $map($expr, $this->compiler));

        $expr = new FieldExpression('foo', array('s' => 'bar baz'));
        $this->assertEquals('fldin_txt_subject:(bar baz)', (string) $map($expr, $this->compiler));

        $expr = new FieldExpression('foo', array('s' => '"bar baz"'));
        $this->assertEquals('facets_subject:"bar baz"', (string) $map($expr, $this->compiler));
    }

    public function testOnlySubjectType()
    {
        $map = $this->map;

        $expr = new FieldExpression('foo', array('t' => 'bar:bar'));
        $this->assertEquals('mrc_d600_s2:(bar\:bar) OR mrc_d601_s2:(bar\:bar)', (string) $map($expr, $this->compiler));

        $expr = new FieldExpression('foo', array('t' => '"bar: baz"'));
        $this->assertEquals('mrc_d600_s2:("bar\: baz") OR mrc_d601_s2:("bar\: baz")', (string) $map($expr, $this->compiler));
    }

    public function testSubjectAndType()
    {
        $map = $this->map;

        $expr = new FieldExpression('foo', array('t' => 'f:oo', 's' => 'b:ar'));
        $this->assertEquals(
            '(sf_d600:"$sa b\:ar $s2 f\:oo"~100 AND mrc_d600_sa:(b\:ar) AND mrc_d600_s2:(f\:oo)) OR (sf_d601:"$sa b\:ar $s2 f\:oo"~100 AND mrc_d601_sa:(b\:ar) AND mrc_d601_s2:(f\:oo))',
            (string) $map($expr, $this->compiler)
        );

        $expr = new FieldExpression('foo', array('t' => 'f:oo', 's' => '"b:ar ah"'));
        $this->assertEquals(
            '(sf_d600:"$sa b\:ar ah $s2 f\:oo"~100 AND mrc_d600_sa:("b\:ar ah") AND mrc_d600_s2:(f\:oo)) OR (sf_d601:"$sa b\:ar ah $s2 f\:oo"~100 AND mrc_d601_sa:("b\:ar ah") AND mrc_d601_s2:(f\:oo))',
            (string) $map($expr, $this->compiler)
        );
    }
}
 