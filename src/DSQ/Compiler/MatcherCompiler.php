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
use UniversalMatcher\MapMatcher;

/**
 * This compiler match expressions with transformation using an UniversalMatcher
 * {@link http://github.com/nicmart/universal-matcher}
 *
 * @package DSQ\Compiler
 */
class MatcherCompiler extends AbstractCompiler
{
    /**
     * @var MapMatcher
     */
    private $matcher;

    public function __construct()
    {
        $this->matcher = new MapMatcher;

        $this->matcher
            ->defineMap('class-and-type', function (Expression $expr) { return get_class($expr) .':' . $expr->getType(); })
            ->defineMap('type', function (Expression $expr) { return $expr->getType(); })
            ->defineMap('class', function (Expression $expr) { return get_class($expr); })
        ;
    }

    /**
     * Set Matcher
     *
     * @param MapMatcher $matcher
     *
     * @return MatcherCompiler The current instance
     */
    public function setMatcher(MapMatcher $matcher)
    {
        $this->matcher = $matcher;
        return $this;
    }

    /**
     * Get Matcher
     *
     * @return \UniversalMatcher\MapMatcher
     */
    public function getMatcher()
    {
        return $this->matcher;
    }


    /**
     * Register a map for the compiler, that matches on type
     *
     * @param string|array $type The type/s of the expressions that will be transformed
     * @param callable $transformation The transformation
     * @throws InvalidTransformationException
     *
     * @return $this                    The current instance
     */
    public function map($type, $transformation)
    {
        if (!is_callable($transformation))
            throw new InvalidTransformationException('Transformations must be callable objects');

        foreach ((array) $type as $t) {
            $this->getMatcher()->rule('type', $t, $transformation);
        }

        return $this;
    }

    /**
     * @param string $type
     * @param string $mapName
     * @return mixed
     */
    public function getMap($type, $mapName = 'type')
    {
        return $this->getMatcher()->matchByMapValue($mapName, $type);
    }

    /**
     * @param string $class
     * @param callable $transformation
     * @return $this
     * @throws InvalidTransformationException
     */
    public function mapByClass($class, $transformation)
    {
        if (!is_callable($transformation))
            throw new InvalidTransformationException('Transformations must be callable objects');

        foreach ((array) $class as $c) {
            $this->getMatcher()->rule('class', $c, $transformation);
        }

        return $this;
    }

    /**
     * @param string $classAndType A string in the form ClassName:type
     * @param callable $transformation
     * @return $this
     * @throws InvalidTransformationException
     */
    public function mapByClassAndType($classAndType, $transformation)
    {
        if (!is_callable($transformation))
            throw new InvalidTransformationException('Transformations must be callable objects');

        foreach ((array) $classAndType as $ct) {
            $this->getMatcher()->rule('class-and-type', $ct, $transformation);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Expression $expression)
    {
        try {
            $transformation = $this->getMatcher()->match($expression);
        } catch (UnregisteredTransformationException $e) {
            throw new UncompilableValueException($e->getMessage());
        }

        return call_user_func($transformation, $expression, $this);
    }
}