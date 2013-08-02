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
     * @param string $class The class of the expression that will be transformed
     * @param string $type The type of the expression that will be transformed
     * @param callable $transformation The transformation
     * @return $this The current instance
     *
     * @throws \InvalidArgumentException
     */
    public function registerTransformation($transformation, $class = '*', $type = '*')
    {
        if (!is_callable($transformation))
            throw new InvalidTransformationException('Transformations must be callable objects');

        $this->transformations[$class][$type] = $transformation;

        return $this;
    }

    /**
     * Get the transformation for the given expression type
     *
     * @param string $class
     * @param string $type
     *
     * @return callable The transformation
     *
     * @throws UnregisteredTransformationException
     */
    public function getTransformation($class, $type)
    {
        if (isset($this->transformations[$class][$type]))
            return $this->transformations[$class][$type];

        if (isset($this->transformations['*'][$type]))
            return $this->transformations['*'][$type];

        if (isset($this->transformations[$class]['*']))
            return $this->transformations[$class]['*'];

        if (isset($this->transformations['*']['*']))
            return $this->transformations['*']['*'];

        throw new UnregisteredTransformationException("There is no transformation that match the selector \"$class:$type\"");
    }

    /**
     * @param string $class
     * @param string $type
     * @return bool
     */
    public function canCompile($class, $type = '*')
    {
        try {
            $this->getTransformation($class, $type);
        } catch (UnregisteredTransformationException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Expression $expression)
    {
        $transformation = $this->getTransformation(get_class($expression), $expression->getType());

        return $transformation($expression, $this);
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