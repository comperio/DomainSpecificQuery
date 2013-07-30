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
                ->unary('-')
                    ->binary('^')
                        ->value('p')
                        ->unary('-', 2)
                    ->end()
                ->end()
                ->binary('^', 'p', '3')
                ->binary('^', 'p', '4')
                ->value('...')
            ->end()
            ->tree('+')
                ->value('1')
                ->value('q')
                ->binary('^', 'q', '2')
                ->binary('^', 'q', '3')
                ->binary('^', 'q', '4')
                ->value('...')
            ->end()
        ->end()
        ->binary('*')
            ->binary('/')
                ->value('1')
                ->binary('-', '1', 'p')
            ->end()
            ->binary('/')
                ->value('1')
                ->binary('-', '1', 'q')
            ->end()
        ->end()
        ->binary('/')
            ->value('1')
            ->binary('*')
                ->binary('-', '1', 'p')
                ->binary('-', '1', 'q')
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