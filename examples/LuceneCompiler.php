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
    ->map('title',
        function (\DSQ\Expression\Expression $expr, \DSQ\Lucene\Compiler\LuceneCompiler $compiler) {
            return new \DSQ\Lucene\FieldExpression('fldin_txt_title',
                $phrase = new \DSQ\Lucene\PhraseExpression($expr->getValue(), 12, 23.5));
    })
    ->mapByClass('DSQ\Expression\FieldExpression', array($compiler, 'fieldExpression'))
;

$builder = new \DSQ\Expression\Builder\ExpressionBuilder('and');

$expression = $builder
    ->field('fieldname', 'ciao a a tutti: io sono Nic')
    ->value('mah')
    ->field('title', 'che bel titolo')
    ->or()
        ->value('ciao')
        ->field('author', 'manzoni alessandro')
        ->binary('>=', 'date', 2012)
        ->binary('<', 'date', 2030)
    ->end()
    ->get();
var_dump($expression);
var_dump((string) $compiler->compile($expression));

var_dump(microtime(true) - $start);
