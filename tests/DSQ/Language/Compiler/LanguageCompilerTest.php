<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Language\Compiler;


use DSQ\Expression\TreeExpression;
use DSQ\Language\Compiler\LanguageCompiler;
use DSQ\Expression\FieldExpression;

class LanguageCompilerTest extends \PHPUnit_Framework_TestCase
{
    public function testCompileFieldExpression()
    {
        $compiler = new LanguageCompiler();

        $this->assertEquals('foo\=bar = baz', $compiler->compile(new FieldExpression('foo=bar', 'baz')));
        $this->assertEquals('foo = (baz baz)', $compiler->compile(new FieldExpression('foo', 'baz baz')));
        $this->assertEquals('foo = "hello \"world\""', $compiler->compile(new FieldExpression('foo', '"hello \"world\""')));

        $this->assertEquals('foo > 2100', $compiler->compile(new FieldExpression('foo', '2100', '>')));
    }

    public function testCompileWithCompositeValues()
    {
        $compiler = new LanguageCompiler();

        $this->assertEquals(
            'foo = (first = a, second = "b", third = (c c), fourth = (d = e))',
            $compiler->compile(new FieldExpression('foo', array('first' => 'a', 'second' => '"b"', 'third' => 'c c', 'fourth' => array('d' => 'e'))))
        );
    }

    public function testCompileTreeExpression()
    {
        $compiler = new LanguageCompiler();
        $tree = new TreeExpression('and');
        $tree
            ->addChild(new FieldExpression('foo', 'bar'))
            ->addChild(new FieldExpression('baz', 'bag'))
        ;

        $this->assertEquals(
            'foo = bar and baz = bag',
            $compiler->compile($tree)
        );

        $tree->addChild($subtree = new TreeExpression('or'));
        $subtree
            ->addChild(new FieldExpression('nic', 'mart'))
            ->addChild(new FieldExpression('hello', 'whole world'))
        ;

        $this->assertEquals(
            'foo = bar and baz = bag and (nic = mart or hello = (whole world))',
            $compiler->compile($tree)
        );
    }

    public function testCompileNotExpression()
    {
        $compiler = new LanguageCompiler();
        $tree = new TreeExpression('not');
        $tree
            ->addChild(new FieldExpression('foo', 'bar'))
        ;

        $this->assertEquals(
            'not foo = bar',
            $compiler->compile($tree)
        );

        $tree->addChild($subtree = new TreeExpression('or'));
        $subtree
            ->addChild(new FieldExpression('nic', 'mart'))
            ->addChild(new FieldExpression('hello', 'whole world'))
        ;

        $this->assertEquals(
            'not (foo = bar or (nic = mart or hello = (whole world)))',
            $compiler->compile($tree)
        );
    }

    public function testCompileToFieldInExpression()
    {
        $compiler = new LanguageCompiler();
        $tree = new TreeExpression('or');
        $tree
            ->addChild(new FieldExpression('foo', 'bar'))
            ->addChild(new FieldExpression('foo', 'baz'))
            ->addChild(new FieldExpression('foo', 'bag'))
        ;

        $this->assertEquals(
            'foo in (bar, baz, bag)',
            $compiler->compile($tree)
        );
    }
}
 