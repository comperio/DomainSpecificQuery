<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Lucene\Compiler;


use DSQ\Compiler\AbstractCompiler;
use DSQ\Compiler\UncompilableValueException;
use DSQ\Expression\Expression;
use DSQ\Lucene\SpanExpression;

class CompositeLuceneCompiler extends AbstractCompiler implements LuceneCompilerInterface
{
    /**
     * @var LuceneCompiler[]
     */
    private $compilers = array();
    private $operator;

    /**
     * @param $operator
     * @param array $compilers
     */
    public function __construct($operator, array $compilers = array())
    {
        foreach ($compilers as $compiler)
            $this->addCompiler($compiler);

        $this->operator = $operator;
    }

    /**
     * @param LuceneCompilerInterface $compiler
     * @return $this
     */
    public function addCompiler(LuceneCompilerInterface $compiler)
    {
        $this->compilers[] = $compiler;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Expression $expression)
    {
        $compileds = array();

        foreach ($this->compilers as $compiler) {
            try {
                $compileds[] = $compiler->compile($expression);
            } catch (UncompilableValueException $e) {}
        }

        if (!$compileds)
            throw new UncompilableValueException("No compiler has been able to compile the expression");

        if (1 == count($compileds))
            return $compileds[0];

        return new SpanExpression($this->operator, $compileds);
    }
} 