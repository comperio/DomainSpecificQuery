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

use DSQ\Compiler\UncompilableValueException;
use DSQ\Expression\BasicExpression;
use DSQ\Lucene\Compiler\CompositeLuceneCompiler;
use DSQ\Lucene\FieldExpression;
use DSQ\Lucene\TermExpression;

class CompositeLuceneCompilerTest extends \PHPUnit_Framework_TestCase
{
    protected $comp1;
    protected $comp2;
    protected $composite;

    public function setUp()
    {
        //first call: only compiler1. second: only compiler 2. Third: both. Fourth: none.
        $comp1 = $this->getMock('DSQ\Lucene\Compiler\LuceneCompilerInterface');
        $comp1->expects($this->any())->method('compile')->will($this->returnCallback(function($expr) {
            static $i = 0;
            $i++;
            if ($i == 2 || $i == 4)
                throw new UncompilableValueException;
            return new FieldExpression('compiler1', 'foo');
        }));
        $comp2 = $this->getMock('DSQ\Lucene\Compiler\LuceneCompilerInterface');
        $comp2->expects($this->any())->method('compile')->will($this->returnCallback(function($expr) {
            static $i = 0;
            $i++;
            if ($i == 1 || $i == 4)
                throw new UncompilableValueException;
            return new FieldExpression('compiler2', 'bar');
        }));

        $this->composite = new CompositeLuceneCompiler('OR', array($comp1, $comp2));
    }

    public function testCompile()
    {
        $expr = new BasicExpression('uninfluent');
        //Only compiler 1.
        $this->assertEquals('compiler1:foo', (string) $this->composite->compile($expr));
        //Only compiler 2
        $this->assertEquals('compiler2:bar', (string) $this->composite->compile($expr));
        //Both
        $this->assertEquals('compiler1:foo OR compiler2:bar', (string) $this->composite->compile($expr));
        //None
        $this->setExpectedException('DSQ\Compiler\UncompilableValueException');
        $this->composite->compile($expr);
    }
}
 