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

/**
 * Class CompositeLuceneCompiler
 * With this compiler you can compose several compilers into a single one. The compiler will return
 * a SpanExpression of the expressions compiled by the subcompilers.
 *
 * @package DSQ\Lucene\Compiler
 */
class CompositeLuceneCompiler extends LuceneCompiler
{
    /**
     * @var LuceneCompiler[]
     */
    private $compilers = array();

    /**
     * @var string
     */
    private $glueOperator;

    /**
     * @param string $glueOperator     The operator of the SpanExpression
     * @param array $compilers         The array of subcompilers
     */
    public function __construct($glueOperator, array $compilers = array())
    {
        foreach ($compilers as $compiler)
            $this->addCompiler($compiler);

        $this->glueOperator = $glueOperator;

        parent::__construct();

        $this->map('*:DSQ\Expression\FieldExpression', array($this, 'terminalExpression'));
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
    public function terminalExpression(Expression $expression)
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

        return new SpanExpression($this->glueOperator, $compileds);
    }
} 