<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Compiler\Label;


use DSQ\Compiler\Label\HumanReadableExpr;
use DSQ\Compiler\Label\LabelCompiler;
use DSQ\Compiler\Label\LabelMapBuilder;
use DSQ\Expression\FieldExpression;

class LabelMapBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var  LabelMapBuilder */
    protected $builder;

    /** @var  LabelCompiler */
    protected $compiler;

    /** @var  FieldExpression */
    protected $fieldExpr;

    public function setUp()
    {
        $this->compiler = $this->getMock('DSQ\Compiler\Label\LabelCompiler', array('getFieldLabel'));
        $this->compiler
            ->expects($this->any())
            ->method('getFieldLabel')
            ->will($this->returnValue('fieldlabel'))
        ;

        $this->builder = new LabelMapBuilder;

        $this->fieldExpr = new FieldExpression('field', 'value');
    }

    public function testAry()
    {
        $b = new LabelMapBuilder();
        $callback = $b->ary(array('value' => 'foo', 'value2' => 'bar'));
        $expected = new HumanReadableExpr('fieldlabel', 'foo');

        $this->assertEquals($expected, $callback($this->fieldExpr, $this->compiler));

        $callback = $b->ary(array('nomatch' => 'samevalue'));
        $expected = new HumanReadableExpr('fieldlabel', 'value');

        $this->assertEquals((string) $expected, (string) $callback($this->fieldExpr, $this->compiler));
    }

    public function testValueCallback()
    {
        $b = new LabelMapBuilder();
        $callback = $b->valueCallback('strtoupper');
        $expected = new HumanReadableExpr('fieldlabel', 'VALUE');

        $this->assertEquals($expected, $callback($this->fieldExpr, $this->compiler));
    }
}
 