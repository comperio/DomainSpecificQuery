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

abstract class AbstractCompiler implements Compiler
{
    /**
     * {@inheritdoc}
     */
    public function compileArray(array $expressions)
    {
        $compileds = array();

        foreach ($expressions as $expression) {
            if (null !== $compiled = $this->compile($expression))
                $compileds[] = $compiled;
        }

        return $compileds;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($expression)
    {
        if ($expression instanceof Expression)
            return $this->compile($expression);

        return $expression;
    }
} 