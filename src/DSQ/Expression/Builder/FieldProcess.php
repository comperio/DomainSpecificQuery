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

use Building\Context;

class FieldProcess extends BinaryProcess
{
    function build(Context $context, $name = null, $value = null, $operator = '=')
    {
        return parent::build($context, $operator, $name, $value, $name);
    }
}