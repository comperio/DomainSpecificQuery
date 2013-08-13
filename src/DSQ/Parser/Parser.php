<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Parser;

use DSQ\Expression\Expression;

interface Parser
{
    /**
     * @param mixed $value      The value to parse
     * @return Expression       The parsed Expression
     */
    public function parse($value);
} 