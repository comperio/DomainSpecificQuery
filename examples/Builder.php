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

use DSQ\Expression\Builder\BinaryBuilder;
use DSQ\Expression\Builder\FieldBuilder;
use DSQ\Expression\Builder\TreeBuilder;
use DSQ\Expression\Builder\ValueBuilder;

$b = new BinaryBuilder();

$b
    ->registerBuilder('binary', $b)
    ->registerBuilder('value', new ValueBuilder())
    ->registerBuilder('field', new FieldBuilder())
    ->registerBuilder('tree', new TreeBuilder())
;

$start = microtime(true);
$b
    ->tree('and')
        ->binary('=')
            ->value('ah')
            ->value('boh')
        ->end()
        ->field('foo', 'bar')
        ->field('baz', 'bug')
        ->tree('or')
            ->field('ba', 'ko')
            ->value('ah')
        ->end()
        ->tree('not', 'a', 'b', 'c')
;

$expr = $b->getExpression();

var_dump(microtime(true) - $start);
var_dump($expr);