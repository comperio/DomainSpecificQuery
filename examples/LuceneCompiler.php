<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

$start = microtime(true);
include '../vendor/autoload.php';

$compiler = new \DSQ\Lucene\Compiler\LuceneCompiler();

$compiler
    ->map(
            'title',
            function (\DSQ\Expression\Expression $expr, \DSQ\Lucene\Compiler\LuceneCompiler $compiler) {
                return new \DSQ\Lucene\FieldExpression('fldin_txt_title',
                    $phrase = new \DSQ\Lucene\PhraseExpression($expr->getValue(), 12, 23.5));
            }
        )
;

$builder = new \DSQ\Expression\Builder\ExpressionBuilder('and');

$expression = $builder
            ->field('date')
                ->binary('range')
                    ->value(1000)->value(2000)
                ->end()
            ->end()
    ->get();

var_dump($expression, (string) $compiler->compile($expression));

$expression = $builder
    ->field('fieldname', 'ciao a a tutti: io sono Nic')
    ->value('mah')
    ->field('title', 'che bel titolo')
    ->or()
        ->value('ciao')
        ->field('author')
            ->value('manzoni alessandro')
        ->end()
        ->binary('>=', 'date', 2012)
        ->binary('<', 'date', 2030)
        ->field('date')
            ->binary('range')
                ->value(1000)->value(2000)
            ->end()
        ->end()
    ->end()
    ->get();

var_dump((string) $compiler->compile($expression));

var_dump(microtime(true) - $start);
