<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Lucene\Compiler\Map;

use DSQ\Compiler\UnregisteredTransformationException;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\Expression;
use DSQ\Lucene\PhraseExpression;
use DSQ\Lucene\SpanExpression;
use DSQ\Lucene\TemplateExpression;
use DSQ\Lucene\TermExpression;
use DSQ\Lucene\BooleanExpression;
use DSQ\Lucene\FieldExpression as LuceneFieldExpression;
use DSQ\Expression\FieldExpression;
use DSQ\Lucene\RangeExpression;

use DSQ\Lucene\Compiler\LuceneCompiler;

/**
 * Class MapBuilder
 * Functional goodness that helps you to build maps for the Lucene compiler
 *
 * @package DSQ\Lucene\Compiler\Map
 */
class MapBuilder
{
    /**
     * Field map
     *
     * @param string $fieldName The name of the solr field
     * @param bool $phrase      is the value a phrase?
     * @param float $boost      Lucene boost
     *
     * @return callable
     */
    public function field($fieldName, $phrase = false, $boost = 1.0)
    {
        return function(FieldExpression $expr, LuceneCompiler $compiler) use ($fieldName, $phrase, $boost)
        {
            $value = $compiler->phrasize($expr->getValue(), $phrase);

            return new LuceneFieldExpression($fieldName, $value, $boost);
        };
    }

    /**
     * Term map
     *
     * @param bool $phrase
     * @param float $boost
     *
     * @return callable
     */
    public function term($phrase = false, $boost = 1.0)
    {
        return function(FieldExpression $expr, LuceneCompiler $compiler) use ($phrase, $boost)
        {
            return new TermExpression($compiler->phrasizeOrTermize($expr->getValue(), $phrase), $boost);
        };
    }

    /**
     * Span map
     *
     * @param string[] $fieldNames  The names of solr fields
     * @param string $op            The spanning operator
     * @param bool $phrase          Is the value a phrase?
     * @param float $boost          The lucene boost
     *
     * @return callable
     */
    public function span(array $fieldNames, $op = 'and', $phrase = false, $boost = 1.0)
    {
        return function(FieldExpression $expr, LuceneCompiler $compiler) use ($fieldNames, $op, $phrase, $boost)
        {
            $value = $compiler->phrasize($expr->getValue(), $phrase);

            $tree = new SpanExpression(strtoupper($op), array(), $boost);

            foreach ($fieldNames as $fieldName) {
                $tree->addExpression(new LuceneFieldExpression($fieldName, $value));
            }

            return $tree;
        };
    }

    /**
     * @param string $fieldName     The solr field name
     * @param float $boost          The Lucene boost
     *
     * @return callable
     */
    public function range($fieldName, $boost = 1.0)
    {
        $that = $this;

        return function(FieldExpression $expr, LuceneCompiler $compiler) use ($fieldName, $boost, $that)
        {
            $val = $expr->getValue();

            if (!is_array($val)) {
                $fieldTransf = $that->field($fieldName, false, $boost);
                return $fieldTransf($expr, $compiler);
            }

            return new LuceneFieldExpression($fieldName, new RangeExpression($val['from'], $val['to']), $boost);
        };
    }

    /**
     * @param string $template  The template
     * @param bool $phrase      Is the value a phrase?
     * @param bool $escape      Has the value to be escaped?
     * @param float $boost      Lucene boost
     *
     * @return callable
     */
    public function template($template, $phrase = false, $escape = true, $boost = 1.0)
    {
        return function(FieldExpression $expr, LuceneCompiler $compiler) use ($template, $phrase, $escape, $boost)
        {
            return new TemplateExpression($template, $compiler->phrasizeOrTermize($expr->getValue(), $phrase, $escape), $boost);
        };
    }

    /**
     * Combine many maps to a single tree one
     *
     * @param string $op        A boolean operator
     * @param callable $map1, ...
     * @return callable
     */
    public function combine($op, $map1/**, $transf2,... */)
    {
        $transformations = func_get_args();
        array_shift($transformations);

        return function(Expression $expr, $compiler) use ($op, $transformations)
        {
            $tree = new SpanExpression(strtoupper($op));

            foreach ($transformations as $transformation) {
                $tree->addExpression($transformation($expr, $compiler));
            }

            return $tree;
        };
    }

    /**
     * This map select the transformation with regexps on the exoression value
     *
     * @param callable[] $regexpsMap
     * @return callable
     */
    public function regexps(array $regexpsMap)
    {
        return function(FieldExpression $expr, $compiler) use ($regexpsMap)
        {
            $value = $expr->getValue();

            foreach ($regexpsMap as $regexp => $transformation) {
                if (preg_match($regexp, $value))
                    return $transformation($expr, $compiler);
            }

            throw new UnregisteredTransformationException("There is no transformation matching the value \"$value\"");
        };
    }

    /**
     * Build a map from a collection of condition and map couples.
     * It will be used the map of the first matched condition.
     *
     * @param callable $condition1
     * @param callable $map1 , ...
     *
     * @return callable
     */
    public function conditional($condition1, $map1/*, $condition2, $map2*/)
    {
        $args = func_get_args();
        return function(Expression $expr, $compiler) use ($args)
        {
            $numargs = count($args);
            for ($i = 0; $i + 1 < $numargs; $i += 2) {
                $condition = $args[$i];
                $map = $args[$i+1];
                if ($condition($expr))
                    return $map($expr, $compiler);
            }
            throw new UnregisteredTransformationException("No condition matched with the given expression");
        };
    }

    /**
     * Before performing the $map transformation, transform the right node value
     * picking a subvalue if it is an array.
     *
     * @param callable $map
     * @param string $key The key of the value array of the right node
     *
     * @return callable
     */
    public function subval($map, $key = 'value')
    {
        return function(FieldExpression $expr, $compiler) use ($key, $map)
        {
            if (is_array($rVal = $expr->getValue())) {
                $val = isset($rVal[$key]) ? $rVal[$key] : '';
                $expr = clone($expr);
                $expr->setValue($val);
            }
            return $map($expr, $compiler);
        };
    }

    /**
     * Set attributes in the resulting expression.
     *
     * @param $map
     * @param array $attributes
     * @return callable
     */
    public function attr($map, array $attributes)
    {
        return function(Expression $expr, $compiler) use ($map, $attributes)
        {
            $luceneExpr = $map($expr, $compiler);

            foreach ($attributes as $name => $value) {
                $luceneExpr[$name] = $value;
            }

            return $luceneExpr;
        };
    }

    /**
     * This is a condition map builder (not a lucene map builder), tought to be
     * used with self::conditional()
     *
     * @param string $key
     * @param bool $notEmpty
     * @return callable
     */
    public function hasSubval($key = 'value', $notEmpty = true)
    {
        return function (FieldExpression $expr) use ($key, $notEmpty)
        {
            $val = $expr->getValue();
            return
                is_array($val)
                && isset($val[$key])
                && ($val[$key] || !$notEmpty);
        };
    }

    /**
     * Condition map builder for constant values.
     *
     * @param mixed $value
     * @return callable
     */
    public function constant($value)
    {
        return function() use ($value) { return $value; };
    }
} 