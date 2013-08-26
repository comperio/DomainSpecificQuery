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

class Context
{
    /** @var  mixed */
    public $object;

    /** @var AbstractBuilder  */
    public $builder;

    /** @var array */
    public $arguments;

    public function __construct($object, AbstractBuilder $builder, array $arguments)
    {
        $this->object = $object;
        $this->builder = $builder;
        $this->arguments = $arguments;
    }
} 