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
class TypeBasedCompiler extends AbstractCompiler
{
    /**
     * @var callable[]
     */
    private $maps = array();

    /**
     * Register a map for the compiler
     *
     * $types can be
     *  - a string. In this case the type selector is $types and the class selector is *
     *  - an array of two scalars. The selector will be $types[0], $types[1]
     *  - an array of arrays of scalars. The map will be added to all selector couples.
     *
     * @param string|array $selectors  The type/s of the expressions that will be transformed
     * @param callable $transformation  The transformation
     * @throws InvalidTransformationException
     * @internal param array|string $classes The class or classes of the expressions that will be transformed
     * @return $this                    The current instance
     *
     */
    public function map($selectors, $transformation)
    {
        if (!is_callable($transformation))
            throw new InvalidTransformationException('Transformations must be callable objects');

        foreach ((array) $selectors as $selector) {
            list($type, $class) = $this->parseSelector($selector);
            $type = strtolower($type);
            $this->maps[$class][$type] = $transformation;
        }

        return $this;
    }

    /**
     * Get the map for the given expression type
     *
     * @param string $type
     * @param string $class
     *
     * @throws UnregisteredTransformationException
     * @return callable The transformation
     *
     */
    public function getMap($type, $class = '*')
    {
        $type = strtolower($type);

        if (isset($this->maps[$class][$type]))
            return $this->maps[$class][$type];

        if (isset($this->maps['*'][$type]))
            return $this->maps['*'][$type];

        if (isset($this->maps[$class]['*']))
            return $this->maps[$class]['*'];

        if (isset($this->maps['*']['*']))
            return $this->maps['*']['*'];

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
            $this->getMap($type, $class);
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
        try {
            $transformation = $this->getMap($expression->getType(), get_class($expression));
        } catch (UnregisteredTransformationException $e) {
            throw new UncompilableValueException($e->getMessage());
        }

        return call_user_func($transformation, $expression, $this);
    }

    /**
     * Selector can be of the forms:
     * "type" or "type:class".
     * Both arguments can be "*", with the meaning of "any value"
     *
     * @param $selector
     * @return array
     */
    private function parseSelector($selector)
    {
        $pieces = explode(":", $selector);

        if (!isset($pieces[1]))
            $pieces[1] = '*';

        return $pieces;
    }
}