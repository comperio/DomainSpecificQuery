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


class FieldExpression extends BasicExpression
{
    /** @var  string */
    private $field;

    /**
     * @param string $field
     * @param string $value
     * @param null $type
     */
    public function __construct($field, $value, $type = null)
    {
        $type = $type ?: $field;
        $this->field = $field;

        parent::__construct($value, $type);
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }
} 