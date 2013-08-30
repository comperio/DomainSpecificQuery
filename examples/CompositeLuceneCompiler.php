<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ;

include '../vendor/autoload.php';

use DSQ\Expression\Builder\ExpressionBuilder;
use DSQ\Lucene\Compiler\CompositeLuceneCompiler;
use DSQ\Lucene\Compiler\LuceneCompiler;
use DSQ\Lucene\Compiler\Map\MapBuilder;

$builder = new ExpressionBuilder;
$m = new MapBuilder();
$comp1 = new LuceneCompiler;
$comp2 = new LuceneCompiler;
$comp3 = new LuceneCompiler;
$composite = new CompositeLuceneCompiler('OR', array($comp1, $comp2, $comp3));

$comp1
    ->map('title', $m->field('man_title'))
    ->map('pages', $m->field('num_pages'))
;

$comp2
    ->map('title', $m->field('auth_title'))
    ->map('name', $m->field('auth_name'))
;

$comp3
    ->map('title', $m->field('item_title'))
    ->map('pages', $m->field('item_pages'))
;

$expr = $builder
    ->or()
        ->field('title', 'all')
        ->field('pages', '2 and 3')
    ->end()
    ->field('name', 'only2')
    ->get();

var_dump((string) $composite->compile($expr));
