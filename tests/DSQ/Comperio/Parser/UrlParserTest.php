<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

class UrlParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \DSQ\Parser\Parser
     */
    protected $parser;

    protected function setUp()
    {
        $this->parser = $this->getMockBuilder('DSQ\Comperio\Parser\UrlParser')
            ->setMethods(array('normalize'))
            ->getMockForAbstractClass();

        $this->parser
            ->expects($this->any())
            ->method('normalize')
            ->will($this->returnValue(array(
                array('and', array(array('foo', 'bar'))),
                array('or', array(array('baz', 'blah'), array('boh', 'buh'))),
            )));
    }

    public function testParse()
    {
        $expr = $this->parser->parse(array());

        $this->assertInstanceOf('DSQ\Expression\TreeExpression', $expr);
        $this->assertEquals('and', $expr->getValue());

        $children = $expr->getChildren();

        $this->assertCount(2, $children);
        $this->assertInstanceOf('DSQ\Expression\TreeExpression', $children[0]);
        $this->assertEquals('and', $children[0]->getValue());
        $this->assertInstanceOf('DSQ\Expression\TreeExpression', $children[1]);
        $this->assertEquals('or', $children[1]->getValue());

        $fields1 = $children[0]->getChildren();
        $fields2 = $children[1]->getChildren();

        $this->assertCount(1, $fields1);
        $this->assertInstanceOf('DSQ\Expression\BinaryExpression', $fields1[0]);
        $this->assertEquals('=', $fields1[0]->getValue());
        $this->assertEquals('foo', $fields1[0]->getLeft()->getValue());
        $this->assertEquals('bar', $fields1[0]->getRight()->getValue());

        $this->assertCount(2, $fields2);
        $this->assertInstanceOf('DSQ\Expression\BinaryExpression', $fields2[1]);
        $this->assertEquals('=', $fields2[0]->getValue());
        $this->assertEquals('baz', $fields2[0]->getLeft()->getValue());
        $this->assertEquals('blah', $fields2[0]->getRight()->getValue());
        $this->assertEquals('=', $fields2[1]->getValue());
        $this->assertEquals('boh', $fields2[1]->getLeft()->getValue());
        $this->assertEquals('buh', $fields2[1]->getRight()->getValue());
    }
}
 