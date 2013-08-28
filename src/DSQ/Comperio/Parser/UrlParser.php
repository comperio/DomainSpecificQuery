<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Comperio\Parser;


use DSQ\Expression\Builder\Builder;
use DSQ\Expression\Builder\ExpressionBuilder;
use DSQ\Parser\Parser;

abstract class UrlParser implements Parser
{
    /**
     * @var ExpressionBuilder
     */
    private $builder;

    /**
     * {@inheritdoc}
     */
    public function parse($value)
    {
        $builder = $this->getBuilder();

        foreach ($this->normalize($value) as $subtreeAry) {
            list($op, $childrenAry) = $subtreeAry;
            $builder->tree($op);

            foreach($childrenAry as $childAry)
                $builder->field($childAry[0], $childAry[1]);

            $builder->end();
        }

        return $builder->get();
    }

    /**
     * Set Builder
     *
     * @param ExpressionBuilder $builder
     *
     * @return $this The current instance
     */
    public function setBuilder(ExpressionBuilder $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * Get Builder
     *
     * @return ExpressionBuilder
     */
    public function getBuilder()
    {
        if (!isset($this->builder))
            $this->builder = new ExpressionBuilder('and');

        return $this->builder;
    }

    /**
     * Transform the value to an array which form is like the one described in
     * @see UrlCompiler::treeToAry()
     *
     * @param array $array
     * @return mixed
     */
    abstract public function normalize(array $array);

    /**
     * Transform a string of the form x_y to the array [x,y]
     * @param string $fieldKey
     * @return array
     */
    protected function fieldAndIndex($fieldKey)
    {
        $result = explode('_', $fieldKey);

        if (!isset($result[1]))
            $result[1] = 1;

        return $result;
    }
} 