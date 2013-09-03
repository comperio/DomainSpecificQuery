<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Compiler;

use DSQ\Expression\Expression;

/**
 * Class CompilerChain
 *
 * This class allows you to chain a set of compilers into a single one.
 * The first added compiler will the first to be used (that's the inverse of the common
 * function composition application order).
 *
 * @package DSQ\Compiler
 */
class CompilerChain extends AbstractCompiler
{
    /**
     * @var Compiler[]
     */
    private $chain = array();

    /**
     * Construct the compiler. It accepts a variable length list of compilers.
     * @param Compiler ...  A variable list of compilers
     */
    public function __construct(/* ... */)
    {
        foreach (func_get_args() as $compiler)
            $this->addCompiler($compiler);
    }

    /**
     * Add a compiler to the chain
     *
     * @param Compiler $compiler
     * @return $this
     */
    public function addCompiler(Compiler $compiler)
    {
        $this->chain[] = $compiler;

        return $this;
    }

    /**
     * {@inheritdocs}
     */
    public function compile(Expression $expression)
    {
        $result = $expression;

        foreach ($this->chain as $compiler)
            $result = $compiler->compile($result);

        return $result;
    }
}