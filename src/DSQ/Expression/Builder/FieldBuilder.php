<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Expression\Builder;

use Building\AbstractBuilder;
use DSQ\Expression\BinaryExpression;

class FieldBuilder extends BinaryBuilder
{
    function processStart($name = null, $value = null, $operator = '=')
    {
        return parent::processStart($operator, $name, $value, $name);
    }
}