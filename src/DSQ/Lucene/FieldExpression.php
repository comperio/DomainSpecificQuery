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

class FieldExpression extends BasicLuceneExpression
{
    /**
     * @param string $fieldname
     * @param string|LuceneExpression $value
     */
    public function __construct($fieldname, $value)
    {
        parent::__construct($value, $fieldname);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->escape($this->getType()) . ':' . $this->escape($this->getValue());
    }
} 