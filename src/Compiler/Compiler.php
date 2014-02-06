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

interface Compiler
{
    /**
     * @param Expression $expression
     * @return mixed
     */
    public function compile(Expression $expression);

    /**
     * Compile an array of expressions. Skip expressions that have been compiled to null
     *
     * @param Expression[] $expressions
     * @return mixed
     */
    public function compileArray(array $expressions);

    /**
     * The same as $this->compile, except that it accept arbitrary values
     * and it acts as identity on non-expression values.
     *
     * @param $expression
     * @return mixed
     */
    public function transform($expression);
} 