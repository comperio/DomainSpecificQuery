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
$start = microtime(true);
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
    ->registerTransformation($compiler->field('facets_class'), '*', 'dewey')
    ->registerTransformation($compiler->field('fldin_txt_fulltextattach'), '*', 'fulltext-atc')
    ->registerTransformation($compiler->field('fldin_txt_class'), '*', 'classtxt')
    ->registerTransformation($compiler->regexps(array(
        '/^"?\d+(\.\d*)?\*?"?$/' => $compiler->getTransformation('*', 'dewey'),
        '/.*/' => $compiler->getTransformation('*', 'classtxt'),
    )), '*', 'class')
    ->registerTransformation($compiler->field('facets_class_desc'), '*', 'facets-class-desc')
    ->registerTransformation($compiler->field('facets_publisher'), '*', 'facets-editore')
    ->registerTransformation(
        $compiler->combine('or',
            $compiler->field('mrc_d210_sa'),
            $compiler->field('mrc_d210_sb'),
            $compiler->field('mrc_d210_sc'),
            $compiler->field('fldint_txt_publisher')
        ), '*', 'publisher'
    )
    // Missing: materiale, facets-materiale (BibtypeSolrSearchField
    ->registerTransformation(
        $compiler->combine('or',
            $compiler->field('mrc_d700_sa'),
            $compiler->field('mrc_d701_sa'),
            $compiler->field('mrc_d702_sa'),
            $compiler->field('mrc_d710_sa'),
            $compiler->field('mrc_d711_sa'),
            $compiler->field('mrc_d712_sa'),
            $compiler->field('mrc_d720_sa'),
            $compiler->field('mrc_d790_sa')
        ), '*', 'aut'
    )
    ->registerTransformation(
        $compiler->combine('or',
            $compiler->field('mrc_d700_s4'),
            $compiler->field('mrc_d701_s4'),
            $compiler->field('mrc_d702_s4'),
            $compiler->field('mrc_d710_s4'),
            $compiler->field('mrc_d711_s4'),
            $compiler->field('mrc_d712_s4'),
            $compiler->field('mrc_d720_s4')
        ), '*', 'aut-role'
    )
    ->registerTransformation($compiler->field('facets_lang'), '*', 'facets-lang')
    ->registerTransformation($compiler->field('sorti_date'), '*', 'facets-date')
    ->registerTransformation($compiler->field('facets_place'), '*', 'facets-place')
    ->registerTransformation($compiler->field('facets_country'), '*', 'facets-country')
    ->registerTransformation($compiler->field('facets_owner'), '*', 'facets-owner')
    ->registerTransformation($compiler->field('facets_printer'), '*', 'facets-printer')
    ->registerTransformation($compiler->field('facets_author'), '*', 'facets-author')
    ->registerTransformation($compiler->field('facets_author_main'), '*', 'facets-author-main')
    ->registerTransformation($compiler->field('fldin_str_authid'), '*', 'id-auth') //Custom...
    ->registerTransformation($compiler->field('fldin_str_subj'), '*', 'id-subj')   //Custom...
    ->registerTransformation($compiler->field('mrc_d901_sb'), '*', 'biblevel')
    ->registerTransformation($compiler->field('mrc_d901_sa'), '*', 'bibtype')
    ->registerTransformation($compiler->field('mrc_cdf'), '*', 'target')
    //missing: facets-target search field
    ->registerTransformation($compiler->field('mrc_d210_sc'), '*', 'pub-name')
    ->registerTransformation($compiler->field('mrc_d210_sa'), '*', 'pub-place')
    ->registerTransformation($compiler->field('mrc_d950_sf'), '*', 'collocation')
    ->registerTransformation($compiler->field('mrc_d921_s3'), '*', 'id-marca') //Custom...
    ->registerTransformation($compiler->field('facets_lang'), '*', 'language')
    ->registerTransformation($compiler->template('fldin_txt_author_main:{}^1000 OR fldin_txt_author:{}^10'), '*', 'autha')
    ->registerTransformation($compiler->field('fldin_txt_owner'), '*', 'owner')
    ->registerTransformation($compiler->field('fldin_txt_printer'), '*', 'printer')
    ->registerTransformation(
        $compiler->template(<<<TPL
            mrc_d200_sa:{}^10000 OR mrc_d200_sc:{}^10000 OR
            mrc_d500_sa:{}^1000 OR mrc_d200_sd:{}^1000 OR mrc_d200_se:{}^1000 OR
            mrc_d200_sh:{}^1000 OR mrc_d200_si:{}^1000 OR
            mrc_d423_st:{}^100 OR mrc_d454_st:{}^100 OR mrc_d461_st:{}^100 OR
            mrc_d327_sa:{}^100 OR mrc_d410_st:{}^100 OR
            fldin_txt_title:{}^10
TPL
        ),  '*', 'title')
    ->registerTransformation(
        $compiler->combine('or', $compiler->field('mrc_d620_sa'), $compiler->field('mrc_d620_sb'), $compiler->field('mrc_d620_sc'), $compiler->field('mrc_d620_sd')),
        '*', 'place')
    ->registerTransformation($compiler->field('sorti_date'), '*', 'year') //Range
    ->registerTransformation($compiler->field('fldis_str_collocation'), '*', 'segnatura')
    ->registerTransformation($compiler->field('id'), '*', 'tid')
    ->registerTransformation($compiler->field('mrc_d500_s3'), '*', 'id-work') // Curstom...
    ->registerTransformation($compiler->term(), '*', 'q')
    //Missing: libarea (LibraryAreaSearchField
    ->registerTransformation($compiler->field('collection', '*', 'collection'))
    //Missing: Facets eta...
    //Missing: loanable
    ->registerTransformation(
        $compiler->combine('or',
            $compiler->field('mrc_d073_sa'),
            $compiler->field('mrc_d010_sa'),
            $compiler->field('mrc_d011_sa'),
            $compiler->field('mrc_d012_sa'),
            $compiler->field('mrc_d013_sa'),
            $compiler->field('mrc_d014_sa'),
            $compiler->field('mrc_d015_sa'),
            $compiler->field('mrc_d016_sa'),
            $compiler->field('mrc_d017_sa')
        ), '*', 'ean'
    )
    //Missing: standard-number: multiplesolrsearchfield
    ->registerTransformation($compiler->field('mrc_d073_sa'), '*', 'num-ean')
    ->registerTransformation($compiler->field('mrc_d010_sa'), '*', 'num-isbn')
    ->registerTransformation($compiler->field('mrc_d011_sa'), '*', 'num-issn')
    ->registerTransformation($compiler->field('mrc_d012_sa'), '*', 'num-fingerprint')
    ->registerTransformation($compiler->field('mrc_d013_sa'), '*', 'num-ismn')
    ->registerTransformation($compiler->field('mrc_d014_sa'), '*', 'num-article')
    ->registerTransformation($compiler->field('mrc_d015_sa'), '*', 'num-isrn')
    ->registerTransformation($compiler->field('mrc_d016_sa'), '*', 'num-isrc')
    ->registerTransformation($compiler->field('mrc_d017_sa'), '*', 'num-other')
    ->registerTransformation($compiler->field('mrc_d020_sa'), '*', 'num-natbib')
    ->registerTransformation($compiler->field('mrc_d021_sa'), '*', 'num-depleg')
    ->registerTransformation($compiler->field('mrc_d022_sa'), '*', 'num-gov')
    ->registerTransformation($compiler->field('mrc_d040_sa'), '*', 'num-coden')
    ->registerTransformation($compiler->field('mrc_d071_sa'), '*', 'num-pub')
    ->registerTransformation($compiler->field('mrc_d072_sa'), '*', 'num-upc')

    ->registerTransformation($compiler->field('bid'), '*', 'fldin_str_bid')
    ->registerTransformation($compiler->template('{}', false, false), '*', 'solr')
    //Missing: cdf
;

$expression = $builder
    //->field('series', 'asd"sd:')
    ->or()
        ->field('class', 'ciao')
        ->field('class', '830')
        ->field('publisher', 'mondadori')
        ->field('solr', 'sorti_date:["2000" TO "2010"]')
    ->getExpression();

echo '<pre>';
echo $compiler->compile($expression);;

var_dump(microtime(true) - $start);