<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Expression;

/**
 * Class BasicExpression
 *
 * The minimal implementation of Expression class
 *
 * @package DSQ\Expression
 */
class BasicExpression implements Expression
{
    private $value;
    private $type;
    private $attributes = array();

    /**
     * @param string $value The name of the expression
     * @param string $type The type of the expression
     */
    public function __construct($value, $type = 'basic')
    {
        $this
            ->setValue($value)
            ->setType($type)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * After-cloning the object
     */
    public function __clone()
    {
        if (is_object($this->getValue()))
            $this->setValue(clone($this->getValue()));
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function attr($name, $defaultValue = null)
    {
        return isset($this[$name]) ? $this[$name] : $defaultValue;
    }

    /**
     * Check if $value is an Expression. If not, wrap it with a BasicExpression
     *
     * @param mixed $value
     *
     * @return Expression
     */
    protected function buildExpression($value)
    {
        if (!$value instanceof Expression)
            return new BasicExpression($value);

        return $value;
    }
} 