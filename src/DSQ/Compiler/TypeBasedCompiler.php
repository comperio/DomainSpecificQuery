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
 * Class TypeBasedCompiler
 *
 * This compiler uses Expression types to match Expressions against the registered
 * set of transformations.
 *
 * @package DSQ\Compiler
 */
class TypeBasedCompiler implements Compiler
{
    /**
     * @var callable[]
     */
    private $transformations = array();

    /**
     * Register a transformation for the compiler
     *
     * @param string $type The type of the expression that will be transformed
     * @param callable $transformation The transformation
     * @return $this The current instance
     *
     * @throws \InvalidArgumentException
     */
    public function registerTransformation($type, $transformation)
    {
        if (!is_callable($transformation))
            throw new InvalidTransformationException('Transformations must be callable objects');

        $this->transformations[$type] = $transformation;

        return $this;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function hasTransformation($type)
    {
        return isset($this->transformations[$type]);
    }

    /**
     * Get the transformation for the given expression type
     *
     * @param string $type
     *
     * @return callable The transformation
     *
     * @throws UnregisteredTransformationException
     */
    public function getTransformation($type)
    {
        if (!$this->hasTransformation($type))
            throw new UnregisteredTransformationException("There is no transformation registered for the Expression type \"$type\"");

        return $this->transformations[$type];
    }

    /**
     * @param Expression $expression
     *
     * @return mixed
     */
    public function compile(Expression $expression)
    {
        $transformation = $this->getTransformation($expression->getType());

        return $transformation($expression, $this);
    }
}