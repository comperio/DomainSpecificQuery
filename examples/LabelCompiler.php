<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

include '../vendor/autoload.php';

use DSQ\Expression\Builder\ExpressionBuilder;
use DSQ\Compiler\Label\LabelCompiler;

$builder = new ExpressionBuilder;
$compiler = new LabelCompiler;

$expr = $builder
    ->field('title', 'foo')
    ->field('date', '2120')
    ->and()
        ->field('name', 'john')
        ->or()
            ->field('lastname', 'smith')
            ->field('lastname', 'rossi')
        ->end()
        ->not()
            ->field('not', 'while')
            ->field('weare', 'together')
        ->end()
    ->end()
    ->get()
;

echo '<pre>', $compiler->compile($expr);
