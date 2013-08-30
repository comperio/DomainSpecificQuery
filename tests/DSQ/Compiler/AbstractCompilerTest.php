<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Compiler;


class AbstractCompilerTest extends \PHPUnit_Framework_TestCase
{
    public function testCompileArray()
    {
        $mockExpr = $this->getMock('DSQ\Expression\Expression');
        $ary = array($mockExpr, $mockExpr, $mockExpr);
        $compiler = $this->getMock('DSQ\Compiler\AbstractCompiler', array('compile'));

        $compiler
            ->expects($this->exactly(3))
            ->method('compile')
            ->with($this->equalTo($mockExpr))
            ->will($this->onConsecutiveCalls('a', null, 'b'))
        ;

        $this->assertEquals(array('a', 'b'), $compiler->compileArray($ary));
    }

    public function testTransform()
    {
        $mockExpr = $this->getMock('DSQ\Expression\Expression');
        $compiler = $this->getMock('DSQ\Compiler\AbstractCompiler', array('compile'));

        $compiler
            ->expects($this->exactly(2))
            ->method('compile')
            ->with($this->equalTo($mockExpr))
            ->will($this->onConsecutiveCalls('a', 'b'))
        ;

        $this->assertEquals('a', $compiler->transform($mockExpr));
        $this->assertEquals('b', $compiler->transform($mockExpr));
        $this->assertEquals('not an expression', $compiler->transform('not an expression'));
    }
}
 