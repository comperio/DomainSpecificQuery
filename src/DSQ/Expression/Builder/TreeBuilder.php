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
use Building\Context;
use DSQ\Expression\TreeExpression;

class TreeBuilder extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     *
     */
    function processStart($value = '')
    {
        $tree = new TreeExpression($value);
        $this->addArgument($tree);

        $children = func_get_args();
        array_shift($children);

        $this->stack[] = new Context($tree, $this, $children);

        if ($children)
            return $this->end();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    function processArgs()
    {
        foreach ($this->context()->arguments as $child) {
            $this->context()->object->addChild($child);
        }
    }


} 