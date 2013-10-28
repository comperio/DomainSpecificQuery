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
            ->defineMap('class-and-type', function (Expression $expr) {
                return get_class($expr) .':' . strtolower($expr->getType()); })
            ->defineMap('type', function (Expression $expr) { return strtolower($expr->getType()); })
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
     * @throws UnregisteredTransformationException
     * @return mixed
     */
    public function getMap($type, $mapName = 'type')
    {
        $map = $this->getMatcher()->matchByMapValue($mapName, $type);

        if (is_callable($map))
            return $map;

        throw new UnregisteredTransformationException("There is no map registered of kind '$mapName' and of type '$type'");
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
     * @param string|array $class The class name or an array of class names
     * @param string|array $type The type value or an array of type values
     * @param callable $transformation
     * @return $this
     * @throws InvalidTransformationException
     */
    public function mapByClassAndType($class, $type, $transformation)
    {
        if (!is_callable($transformation))
            throw new InvalidTransformationException('Transformations must be callable objects');

        foreach ((array) $class as $c) {
            foreach ((array) $type as $t) {
                $this->getMatcher()->rule('class-and-type', "$c:$t", $transformation);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Expression $expression)
    {
        $transformation = $this->getMatcher()->match($expression);

        if (is_callable($transformation) )
            return call_user_func($transformation, $expression, $this);

        throw new UncompilableValueException('There are no registered tranformations that match the given expression');
    }
}