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

$builder = new \DSQ\Expression\Builder\Builder();
$compiler = new \DSQ\Compiler\StringCompiler\StringCompiler();

$expression = $builder
    ->tree('=')
        ->tree('*')
            ->tree('+')
                ->value('1')
                ->value('p')
                ->field('p', '2', '^')
                ->field('p', '3', '^')
                ->field('p', '4', '^')
                ->value('...')
            ->end()
            ->tree('+')
                ->value('1')
                ->value('q')
                ->field('q', '2', '^')
                ->field('q', '3', '^')
                ->field('q', '4', '^')
                ->value('...')
            ->end()
        ->end()
        ->tree('*')
            ->tree('/')
                ->value('1')
                ->field('1', 'p', '-')
            ->end()
            ->tree('/')
                ->value('1')
                ->field('1', 'q', '-')
            ->end()
        ->end()
        ->tree('/')
            ->value('1')
            ->tree('*')
                ->field('1', 'p', '-')
                ->field('1', 'q', '-')
            ->end()
        ->end()
    ->getExpression();

var_dump($compiler->compile($expression));

$expression = $builder
    ->tree('and')
        ->field('giulio', 'bonanome')
        ->field('ciro', 'mattia')
        ->tree('or')
            ->field('titolo', 'la volpe del polesine')
            ->field('author', 'nicolò martini')
        ->end()
    ->getExpression();

var_dump($compiler->compile($expression));