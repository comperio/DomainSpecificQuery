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
use DSQ\Expression\BasicExpression;
use DSQ\Expression\Expression;

class ValueBuilder extends AbstractBuilder
{
    function processStart($value = '', $type = null)
    {
        $this->addArgument(new BasicExpression($value, $type));

        return $this->context()->builder;
    }

    /**
     * Do object manipulation using context args.
     *
     * @return mixed
     */
    function processArgs()
    {
        //Do nothing
    }
}