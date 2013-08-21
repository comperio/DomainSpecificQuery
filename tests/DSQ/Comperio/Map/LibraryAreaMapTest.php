<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

use DSQ\Expression\BinaryExpression;
use DSQ\Comperio\Compiler\Map\LibraryAreaMap;
use DSQ\Lucene\Compiler\LuceneCompiler;
use DSQ\Lucene\LuceneQuery;

class LibraryAreaMapTest extends PHPUnit_Framework_TestCase
{

    public function testInvoke()
    {
        $expr = new BinaryExpression('=', 'foo', 1);
        $areaMap = array(
            1 => array(1, 2, 3)
        );
        $map = new LibraryAreaMap($areaMap);

        $this->assertEquals('faceti_libvisi:1 OR faceti_libvisi:2 OR faceti_libvisi:3', (string) $map($expr, new LuceneCompiler));

        $expr->setRight(2);
        $this->assertEquals(LuceneQuery::EMPTYQUERY, (string) $map($expr, new LuceneCompiler));
    }
}
 