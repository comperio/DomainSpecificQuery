<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Comperio\Compiler\Map;


use DSQ\Expression\BinaryExpression;
use DSQ\Lucene\AbstractLuceneExpression;
use DSQ\Lucene\Compiler\LuceneCompiler;
use DSQ\Lucene\Compiler\Map\MapBuilder;
use DSQ\Lucene\LuceneQuery;
use DSQ\Lucene\PureExpression;
use DSQ\Lucene\TermExpression;
use StringTemplate\AbstractEngine;
use StringTemplate\Engine;

class SubjTypeMap
{
    /**
     * @var AbstractEngine
     */
    private $engine;

    /**
     * @var MapBuilder
     */
    private $mapBuilder;

    private $fields = array(
        600, 601, 602, 603, 604, 605, 606, 607, 608, 609, 610
    );

    /**
     * @param AbstractEngine $engine
     * @param MapBuilder $mapBuilder
     * @param array $fields
     */
    public function __construct(AbstractEngine $engine = null, MapBuilder $mapBuilder = null, array $fields = null)
    {
        $this->engine = $engine ?: new Engine;
        $this->mapBuilder = $mapBuilder ?: new MapBuilder;
        if ($fields)
            $this->fields = $fields;
    }

    /**
     * @param BinaryExpression $expr
     * @param LuceneCompiler $compiler
     * @return PureExpression
     */
    public function __invoke(BinaryExpression $expr, $compiler)
    {
        $exprValue = $expr->getRight()->getValue();

        foreach ($exprValue as &$value) {
            $value = (string) new TermExpression($value); //escape the value
        }

        $value = $this->engine->render($this->template($exprValue), array(
            's' => isset($exprValue['s']) ? $exprValue['s'] : '',
            't' => isset($exprValue['t']) ? $exprValue['t'] : '',
            's_unquoted' => isset($exprValue['s']) ? $this->unquote($exprValue['s']) : '',
            't_unquoted' => isset($exprValue['t']) ? $this->unquote($exprValue['t']) : '',
        ));

        return new PureExpression($value);
    }

    /**
     * Compute the template that will be rendered
     *
     * @param array $value  The value of the expression
     * @return string
     */
    private function template($value)
    {
        if (isset($value['s']) && isset($value['t']))
            return $this->templateTypeAndSubject();

        if (isset($value['t']))
            return $this->templateType();

        if (isset($value['s']))
            return $this->templateSubject($value['s']);

        return LuceneQuery::ALLQUERY;
    }

    /**
     * @return string
     */
    private function templateTypeAndSubject()
    {
        return implode(' OR ', array_map(function($field){
            return sprintf('(sf_d%d:"$sa {s_unquoted} $s2 {t_unquoted}"~100 AND mrc_d%d_sa:({s}) AND mrc_d%d_s2:({t}))',
                $field, $field, $field);
        }, $this->fields));
    }

    /**
     * @return string
     */
    private function templateType()
    {
        return implode(' OR ', array_map(function($field){
            return sprintf('mrc_d%d_s2:({t})',
                $field);
        }, $this->fields));
    }

    /**
     * @param string $subject
     * @return string
     */
    private function templateSubject($subject)
    {
        if ($this->isQuoted($subject)) {
            $tpl = 'facets_subject:{s}';
        } else {
            //This add plus between words: missing
            $tpl = 'fldin_txt_subject:({s})';
        }

        return $tpl;
    }

    /**
     * @param string $string
     * @return bool
     */
    private function isQuoted($string)
    {
        return $string[0] === '"' && $string[strlen($string)-1] === '"';
    }

    /**
     * @param string $string
     * @return string
     */
    private function unquote($string)
    {
        if ($this->isQuoted($string))
            return substr($string, 1, -1);

        return $string;
    }
}