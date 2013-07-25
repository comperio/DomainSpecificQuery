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

interface Expression
{
    /**
     * @param string $name The name of the expression
     * @return $this The current instance
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();
} 