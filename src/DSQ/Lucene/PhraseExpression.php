<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Lucene;

class PhraseExpression extends BasicLuceneExpression
{
    private $slope = 0;

    /**
     * @param string $value
     * @param int $slope
     * @param string $type
     */
    public function __construct($value, $slope = 0, $type = 'phrase')
    {
        parent::__construct($value, $type);

        $this->slope = $slope;
    }


    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $slopeSuffix = $this->slope != 0 ? '~' . $this->slope : '';
        
        return '"' . $this->escape_phrase($this->getValue()) . '"' . $slopeSuffix;
    }
} 