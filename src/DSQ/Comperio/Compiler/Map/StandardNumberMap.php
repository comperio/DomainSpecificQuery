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
use DSQ\Lucene\FieldExpression;
use DSQ\Lucene\SpanExpression;

class StandardNumberMap
{
    private $numbers = array(
        'EAN' => 'mrc_d073_sa',
        'ISBN' => 'mrc_d010_sa',
        'ISSN' => 'mrc_d011_sa',
        'FINGERPRINT' => 'mrc_d012_sa',
        'ISMN' => 'mrc_d013_sa',
        'ARTICLE' => 'mrc_d014_sa',
        'ISRN' => 'mrc_d015_sa',
        'ISRC' => 'mrc_d016_sa',
        'OTHER' => 'mrc_d017_sa',
        'NLN' => 'mrc_d020_sa', #National Library Number
        'LDN' => 'mrc_d021_sa', #Legal Deposit Number
        'GOV' => 'mrc_d022_sa', #Governative number
        'CODEN' => 'mrc_d040_sa', #CODEN
        'PN' => 'mrc_d071_sa', #Publisher Number
        'UPC' => 'mrc_d072_sa',
    );

    /**
     * @param null $numbers
     */
    public function __construct($numbers = null)
    {
        if ($numbers)
            $this->numbers = $numbers;
    }

    /**
     * @param BinaryExpression $expr
     * @return FieldExpression|SpanExpression
     */
    public function __invoke(BinaryExpression $expr)
    {
        $rightVal = $expr->getRight()->getValue();

        if ($this->isSingleNumberValue($rightVal))
            return new FieldExpression($this->numbers[$rightVal['subfield']], $rightVal['value']);

        return $this->allNumbersExpression(isset($rightVal['value']) ? $rightVal['value'] : $rightVal);
    }

    /**
     * @param string|array $value
     * @return bool
     */
    private function isSingleNumberValue($value)
    {
        return
            is_array($value)
            && isset($value['subfield'])
            && isset($this->numbers[$value['subfield']])
            && isset($value['value'])
        ;
    }

    /**
     * @param $value
     * @return SpanExpression
     */
    private function allNumbersExpression($value)
    {
        $span = new SpanExpression('OR');

        foreach ($this->numbers as $field)
            $span->addExpression(new FieldExpression($field, $value));

        return $span;
    }
} 