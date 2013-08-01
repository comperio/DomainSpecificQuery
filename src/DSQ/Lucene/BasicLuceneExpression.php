<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Lucene;

use DSQ\Expression\BasicExpression;

class BasicLuceneExpression extends BasicExpression implements LuceneExpression
{
    private $boost = 1.0;

    /**
     * @param mixed|LuceneExpression $value      The value of the expression
     * @param float $boost                       The boost factor
     * @param string $type                       The type of the expression
     */
    public function __construct($value, $boost = 1.0, $type = 'basic')
    {
        parent::__construct($value, $type);

        $this->setBoost($boost);
    }

    /**
     * {@inheritdoc}
     */
    public function setDeepValue($value)
    {
        $curVal = $this->getValue();
        if ($curVal instanceof LuceneExpression) {
            $curVal->setDeepValue($value);
        } else {
            $this->setValue($value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setBoost($boost)
    {
        $this->boost = (float) $boost;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBoost()
    {
        return $this->boost;
    }

    /**
     * Escape a string to be a suitable lucene value
     *
     * @param string $expression
     * @return mixed
     */
    public static function escape($expression)
    {
        if ($expression instanceof LuceneExpression)
            return $expression;

        //list taken from http://lucene.apache.org/java/docs/queryparsersyntax.html#Escaping%20Special%20Characters
        //Removed *, ? and ^.
        return preg_replace('/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|~|:|\\\)/', '\\\$1', $expression);
    }

    /**
     * Escape an expression for quoted values
     *
     * @param string|LuceneExpression $expression
     * @return string|LuceneExpression
     */
    public static function escape_phrase($expression)
    {
        if ($expression instanceof LuceneExpression)
            return $expression;

        return preg_replace('/("|\\\)/', '\\\$1', $expression);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->escape($this->getValue()) . $this->boostSuffix();
    }

    /**
     * @return string
     */
    protected function boostSuffix()
    {
        return $this->boost != 1.0
            ? "^{$this->boost}"
            : ''
        ;
    }
}