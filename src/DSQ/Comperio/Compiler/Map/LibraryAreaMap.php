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
use DSQ\Lucene\Compiler\Map\MapBuilder;
use DSQ\Lucene\FieldExpression as LuceneFieldExpression;
use DSQ\Expression\FieldExpression;
use DSQ\Lucene\SpanExpression;

/**
 * Class LibraryAreaMap
 * @package DSQ\Comperio\Compiler\Map
 */
class LibraryAreaMap
{
    private $libAreas = array();

    /**
     * @param array $libAreas
     */
    public function __construct(array $libAreas)
    {
        $this->libAreas = $libAreas;
    }

    /**
     * @param FieldExpression $expr
     * @param $compiler
     * @return SpanExpression
     */
    public function __invoke(FieldExpression $expr, $compiler)
    {
        $span = new SpanExpression('OR');
        $areaId = $expr->getValue();
        $libIds = isset($this->libAreas[$areaId]) ? $this->libAreas[$areaId] : array();

        foreach ($libIds as $id) {
            $span->addExpression(new LuceneFieldExpression('faceti_libvisi', $id));
        }

        return $span;
    }
} 