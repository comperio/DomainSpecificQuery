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

use DSQ\Compiler\LuceneCompiler\LuceneCompiler;
use DSQ\Expression\Builder\Builder;

include '../vendor/autoload.php';

$compiler = new LuceneCompiler;
$builder = new Builder;

$compiler
    ->registerTransformation($compiler->field('faceti_libvisi'), '*', 'home-lib')
    ->registerTransformation($compiler->field('facets_biblevel_full'), '*', 'facets-biblevel-full')
    ->registerTransformation($compiler->field('facets_biblevel'), '*', 'facets-biblevel-full')
    ->registerTransformation($compiler->field('facets_subject'), '*', 'facets-subject')
    ->registerTransformation($compiler->field('fldin_txt_subject'), '*', 'subject')
    ->registerTransformation($compiler->field('mrc_d610_s9'), '*', 'subject-type')
    //missing: subj-and-type
    ->registerTransformation(
        $compiler->combine('or',
            $compiler->template($seriesTpl = '(mrc_d901_sb:"c" AND (mrc_d200_sa:{}^100 OR mrc_d200_sc:{}^1000 OR mrc_d200_sd:{}^1000 OR mrc_d200_se:{} OR mrc_d200_sh:{} OR mrc_d200_si:{})) OR (mrc_d225_sa:{} OR mrc_d410_st:{})'),
            $compiler->template($seriesTpl, true)
        ), '*', 'series'
    )
;

$expression = $builder
    ->field('series', 'asd"sd:')
    ->getExpression();

var_dump((string) $compiler->compile($expression));