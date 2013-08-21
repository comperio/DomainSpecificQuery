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

interface Expression extends \ArrayAccess
{
    /**
     * @param string $type The type of the expression
     * @return $this The current instance
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $value The name of the expression
     * @return $this The current instance
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function getValue();
} 