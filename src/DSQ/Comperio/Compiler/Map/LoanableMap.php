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


use DSQ\Lucene\FieldExpression as LuceneFieldExpression;
use DSQ\Expression\FieldExpression;
use DSQ\Lucene\LuceneQuery;
use DSQ\Lucene\MatchAllExpression;
use DSQ\Lucene\RangeExpression;
use DSQ\Lucene\SpanExpression;

class LoanableMap
{
    /**
     * @var \DateTime
     */
    private $day;

    /**
     * @param \DateTime $day
     */
    public function __construct(\DateTime $day = null)
    {
        if (!$day)
            $day = new \DateTime;

        $this->day = $day;
    }

    /**
     * @param FieldExpression $expr
     * @param $compiler
     * @return SpanExpression
     */
    public function __invoke(FieldExpression $expr, $compiler)
    {
        $stringDate = $this->getDateTime($expr->getValue())->format('Y-m-d');

        $fieldExpr = new LuceneFieldExpression('mrc_d901_sl', new RangeExpression($stringDate, '*'));
        return new SpanExpression('NOT', array(new MatchAllExpression, $fieldExpr));
    }

    /**
     * @param int $index
     * @return \DateTime
     */
    private function getDateTime($index)
    {
        switch ($index) {
            case 0:
                $days = 1;
                break;
            case 1:
                $days = 2;
                break;
            default:
                $days = 8;
        }
        $datetime = clone $this->day;
        $datetime->modify("+$days days");

        return $datetime;
    }
} 