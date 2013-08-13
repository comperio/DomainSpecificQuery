<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\ComperioParser;


use DSQ\Expression\Builder\Builder;
use DSQ\Parser\Parser;

abstract class UrlParser implements Parser
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * {@inheritdoc}
     */
    public function parse($value)
    {
        $builder = $this->getBuilder();

        $builder->tree('and');

        foreach ($this->normalize($value) as $subtreeAry) {
            list($op, $childrenAry) = $subtreeAry;
            $builder->tree($op);

            foreach($childrenAry as $childAry)
                $builder->field($childAry[0], $childAry[1]);

            $builder->end();
        }

        return $builder->getExpression();
    }

    /**
     * Set Builder
     *
     * @param \DSQ\Expression\Builder\Builder $builder
     *
     * @return $this The current instance
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * Get Builder
     *
     * @return Builder
     */
    public function getBuilder()
    {
        if (!isset($this->builder))
            $this->builder = new Builder;

        return $this->builder;
    }

    /**
     * Transform the value to an array which form is like the one described in
     * @see UrlCompiler::treeToAry()
     *
     * @param $value
     * @return mixed
     */
    abstract function normalize($value);
} 