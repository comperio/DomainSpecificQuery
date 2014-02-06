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


use DSQ\Compiler\CompilerChain;
use DSQ\Expression\BasicExpression;
use DSQ\Expression\Expression;

class CompilerChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int $expectedcalls
     * @return Compiler
     */
    public function getCompilerMock($expectedcalls = 1)
    {
        $mock = $this->getMock('DSQ\Compiler\Compiler');
        $mock
            ->expects($this->exactly($expectedcalls))
            ->method('compile')
            ->will($this->returnCallback(function(Expression $expr){
                return new BasicExpression((int) $expr->getValue() + 1);
            }));
        return $mock;
    }

    public function testConstructor()
    {
        $chain = array($this->getMock('DSQ\Compiler\Compiler'), $this->getMock('DSQ\Compiler\Compiler'), $this->getMock('DSQ\Compiler\Compiler'));
        $compiler = new CompilerChain($chain[0], $chain[1], $chain[2]);

        $this->assertAttributeEquals($chain, 'chain', $compiler);
    }

    public function testAddCompiler()
    {
        $chain = array($this->getMock('DSQ\Compiler\Compiler'), $this->getMock('DSQ\Compiler\Compiler'), $this->getMock('DSQ\Compiler\Compiler'));
        $compiler = new CompilerChain;
        $compiler
            ->addCompiler($chain[0])
            ->addCompiler($chain[1])
            ->addCompiler($chain[2]);

        $this->assertAttributeEquals($chain, 'chain', $compiler);
    }

    public function testCompile()
    {
        $expr = new BasicExpression(0);
        $chain = new CompilerChain($this->getCompilerMock(1), $this->getCompilerMock(1), $this->getCompilerMock(1));

        $compiled = $chain->compile($expr);

        $this->assertEquals(3, $compiled->getValue());
    }
}
 