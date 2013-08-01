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

use DSQ\Expression\Expression;

interface LuceneExpression extends Expression
{
    /**
     * This converts the expression to a lucene statement
     *
     * @return string
     */
    public function __toString();

    /**
     * Set Boost
     *
     * @param float $boost  The boost factor
     *
     * @return $this        The current instance
     */
    public function setBoost($boost);

    /**
     * Get Boost
     *
     * @return float        The boost factor
     */
    public function getBoost();

    /**
     * Set the given value to the deepest node in the expressio chain
     *
     * @param mixed|LuceneExpression $value  The value to be set
     *
     * @return $this                         The current instance
     */
    public function setDeepValue($value);
} 