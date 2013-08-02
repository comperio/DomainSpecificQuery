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

class FieldExpression extends AbstractLuceneExpression
{
    /**
     * @param string $fieldname
     * @param string|LuceneExpression $value
     * @param float $boost
     */
    public function __construct($fieldname, $value, $boost = 1.0)
    {
        parent::__construct($this->expr($value), $boost, $fieldname);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $value = $this->getValue();
        $escapedValue = $this->escape($value);

        if ($value instanceof LuceneExpression && !$value->hasPrecedence($this))
            $escapedValue = "($escapedValue)";

        return $this->escape($this->getType()) . ':' . $escapedValue . $this->boostSuffix();
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrecedence($expression)
    {
        return true;
    }
}