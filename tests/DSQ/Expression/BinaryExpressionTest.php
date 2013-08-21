<?php
/*
 * This file is part of DomainSpecificQuery.
 *
 * (c) 2013 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DSQ\Test\Expression;

use DSQ\Expression\Expression;
use DSQ\Expression\BasicExpression;
use DSQ\Expression\BinaryExpression;

/**
 * Unit tests for class FirstClass
 */
class BinaryExpressionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BinaryExpression
     */
    protected $expression;

    /**
     * @var Expression
     */
    protected $left;

    /**
     * @var Expression
     */
    protected $right;

    public function setUp()
    {
        $this->left = new BasicExpression('left');
        $this->right = new BasicExpression('right');

        $this->expression = new BinaryExpression('foo', $this->left, $this->right);
    }

    public function testSetAndGetLeftRight()
    {
        $this->assertEquals($this->left, $this->expression->getLeft());
        $this->assertEquals($this->right, $this->expression->getRight());

        $this->expression
            ->setRight($this->left)
            ->setLeft($this->right)
        ;

        $this->assertEquals($this->left, $this->expression->getRight());
        $this->assertEquals($this->right, $this->expression->getLeft());
    }

    public function testSetAndGetLeftRightTransformsNonExpressionValuesToBasicExpressions()
    {
        $this->expression
            ->setLeft("yoo I'm at left!!!")
            ->setRight("I'm right!!")
        ;

        $left = $this->expression->getLeft();
        $right = $this->expression->getRight();

        $this->assertInstanceOf('Dsq\Expression\BasicExpression', $left);
        $this->assertEquals("yoo I'm at left!!!", $left->getValue());

        $this->assertInstanceOf('DSQ\Expression\BasicExpression', $right);
        $this->assertEquals("I'm right!!", $right->getValue());
    }

    public function testClone()
    {
        $this->expression
            ->setLeft("left")
            ->setRight("right")
        ;

        $cloned = clone($this->expression);

        $cloned
            ->setValue('foofoo')
            ->setLeft("leftleft")
            ->getRight()->setValue("rightright");

        $this->assertEquals('foo', $this->expression->getValue());
        $this->assertEquals('left', $this->expression->getLeft()->getValue());
        $this->assertEquals('right', $this->expression->getRight()->getValue());
    }
}