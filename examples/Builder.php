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

ini_set('xdebug.var_display_max_depth', '10');

use DSQ\Expression\Builder\BinaryBuilder;
use DSQ\Expression\Builder\ExpressionBuilder;
use DSQ\Expression\Builder\FieldProcess;
use DSQ\Expression\Builder\TreeProcess;
use DSQ\Expression\Builder\ValueProcess;

$b = new ExpressionBuilder('and');

$start = microtime(true);
$b
    ->filter('first', 'firstValue')
    ->exclude('gabba', 'medas')
    ->exclude()
        ->field('nic', 'mart')
    ->end()
    ->filter()
        ->field('ciao', 'mamma')
        ->field('ciao', 'asdsds')
        ->or()
            ->field('title', '[* TO *]')
            ->field('author', 'boh')
        ->end()
    ->end()
    ->value('ah')
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
    ->not()
        ->field('not me', 'ah')
    ->end()
;

$expr = $b->get();

var_dump(microtime(true) - $start);
var_dump($expr);
