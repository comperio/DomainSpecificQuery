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

use DSQ\Lucene\Compiler\LuceneCompiler;
use DSQ\Expression\Builder\Builder;

include '../vendor/autoload.php';
$start = microtime(true);
$compiler = new LuceneCompiler;
$builder = new Builder;

$compiler
    ->map('home-lib', $compiler->field('faceti_libvisi'))
    ->map('facets-biblevel-full', $compiler->field('facets_biblevel_full'))
    ->map('facets-biblevel-full', $compiler->field('facets_biblevel'))
    ->map('facets-subject', $compiler->field('facets_subject'))
    ->map('subject', $compiler->field('fldin_txt_subject'))
    ->map('subject-type', $compiler->field('mrc_d610_s9'))
    //missing: subj-and-type
    ->map(
            'series', $compiler->combine(
                'or', $compiler->template(
                    $seriesTpl
                            = '(mrc_d901_sb:"c" AND (mrc_d200_sa:{}^100 OR mrc_d200_sc:{}^1000 OR mrc_d200_sd:{}^1000 OR mrc_d200_se:{} OR mrc_d200_sh:{} OR mrc_d200_si:{})) OR (mrc_d225_sa:{} OR mrc_d410_st:{})'
                ), $compiler->template($seriesTpl, true)
            )
    )
    ->map('dewey', $compiler->field('facets_class'))
    ->map('fulltext-atc', $compiler->field('fldin_txt_fulltextattach'))
    ->map('classtxt', $compiler->field('fldin_txt_class'))
    ->map(
            'class', $compiler->regexps(
                array(
                    '/^"?\d+(\.\d*)?\*?"?$/' => $compiler->getMap('*', 'dewey'),
                    '/.*/' => $compiler->getMap('*', 'classtxt'),
                )
            )
        )
    ->map('facets-class-desc', $compiler->field('facets_class_desc'))
    ->map('facets-editore', $compiler->field('facets_publisher'))
    ->map(
            'publisher', $compiler->combine(
                'or', $compiler->field('mrc_d210_sa'), $compiler->field('mrc_d210_sb'), $compiler->field('mrc_d210_sc'),
                $compiler->field('fldint_txt_publisher')
            )
    )
    // Missing: materiale, facets-materiale (BibtypeSolrSearchField
    ->map(
            'aut', $compiler->combine(
                'or', $compiler->field('mrc_d700_sa'), $compiler->field('mrc_d701_sa'), $compiler->field('mrc_d702_sa'),
                $compiler->field('mrc_d710_sa'), $compiler->field('mrc_d711_sa'), $compiler->field('mrc_d712_sa'),
                $compiler->field('mrc_d720_sa'), $compiler->field('mrc_d790_sa')
            )
    )
    ->map(
            'aut-role', $compiler->combine(
                'or', $compiler->field('mrc_d700_s4'), $compiler->field('mrc_d701_s4'), $compiler->field('mrc_d702_s4'),
                $compiler->field('mrc_d710_s4'), $compiler->field('mrc_d711_s4'), $compiler->field('mrc_d712_s4'),
                $compiler->field('mrc_d720_s4')
            )
    )
    ->map('facets-lang', $compiler->field('facets_lang'))
    ->map('facets-date', $compiler->field('sorti_date'))
    ->map('facets-place', $compiler->field('facets_place'))
    ->map('facets-country', $compiler->field('facets_country'))
    ->map('facets-owner', $compiler->field('facets_owner'))
    ->map('facets-printer', $compiler->field('facets_printer'))
    ->map('facets-author', $compiler->field('facets_author'))
    ->map('facets-author-main', $compiler->field('facets_author_main'))
    ->map('id-auth', $compiler->field('fldin_str_authid')) //Custom...
    ->map('id-subj', $compiler->field('fldin_str_subj'))   //Custom...
    ->map('biblevel', $compiler->field('mrc_d901_sb'))
    ->map('bibtype', $compiler->field('mrc_d901_sa'))
    ->map('target', $compiler->field('mrc_cdf'))
    //missing: facets-target search field
    ->map('pub-name', $compiler->field('mrc_d210_sc'))
    ->map('pub-place', $compiler->field('mrc_d210_sa'))
    ->map('collocation', $compiler->field('mrc_d950_sf'))
    ->map('id-marca', $compiler->field('mrc_d921_s3')) //Custom...
    ->map('language', $compiler->field('facets_lang'))
    ->map('autha', $compiler->template('fldin_txt_author_main:{}^1000 OR fldin_txt_author:{}^10'))
    ->map('owner', $compiler->field('fldin_txt_owner'))
    ->map('printer', $compiler->field('fldin_txt_printer'))
    ->map(
            'title', $compiler->template(
                <<<TPL
                            mrc_d200_sa:{}^10000 OR mrc_d200_sc:{}^10000 OR
            mrc_d500_sa:{}^1000 OR mrc_d200_sd:{}^1000 OR mrc_d200_se:{}^1000 OR
            mrc_d200_sh:{}^1000 OR mrc_d200_si:{}^1000 OR
            mrc_d423_st:{}^100 OR mrc_d454_st:{}^100 OR mrc_d461_st:{}^100 OR
            mrc_d327_sa:{}^100 OR mrc_d410_st:{}^100 OR
            fldin_txt_title:{}^10
TPL
            )
        )
    ->map(
            'place', $compiler->combine(
                'or', $compiler->field('mrc_d620_sa'), $compiler->field('mrc_d620_sb'), $compiler->field('mrc_d620_sc'),
                $compiler->field('mrc_d620_sd')
            )
        )
    ->map('year', $compiler->range('sorti_date'))
    ->map('segnatura', $compiler->field('fldis_str_collocation'))
    ->map('tid', $compiler->field('id'))
    ->map('id-work', $compiler->field('mrc_d500_s3')) // Curstom...
    ->map('q', $compiler->term())
    //Missing: libarea (LibraryAreaSearchField
    ->map('collection', $compiler->field('collection', '*', 'collection'))
    //Missing: Facets eta...
    //Missing: loanable
    ->map(
            'ean', $compiler->combine(
                'or', $compiler->field('mrc_d073_sa'), $compiler->field('mrc_d010_sa'), $compiler->field('mrc_d011_sa'),
                $compiler->field('mrc_d012_sa'), $compiler->field('mrc_d013_sa'), $compiler->field('mrc_d014_sa'),
                $compiler->field('mrc_d015_sa'), $compiler->field('mrc_d016_sa'), $compiler->field('mrc_d017_sa')
            )
    )
    //Missing: standard-number: multiplesolrsearchfield
    ->map('num-ean', $compiler->field('mrc_d073_sa'))
    ->map('num-isbn', $compiler->field('mrc_d010_sa'))
    ->map('num-issn', $compiler->field('mrc_d011_sa'))
    ->map('num-fingerprint', $compiler->field('mrc_d012_sa'))
    ->map('num-ismn', $compiler->field('mrc_d013_sa'))
    ->map('num-article', $compiler->field('mrc_d014_sa'))
    ->map('num-isrn', $compiler->field('mrc_d015_sa'))
    ->map('num-isrc', $compiler->field('mrc_d016_sa'))
    ->map('num-other', $compiler->field('mrc_d017_sa'))
    ->map('num-natbib', $compiler->field('mrc_d020_sa'))
    ->map('num-depleg', $compiler->field('mrc_d021_sa'))
    ->map('num-gov', $compiler->field('mrc_d022_sa'))
    ->map('num-coden', $compiler->field('mrc_d040_sa'))
    ->map('num-pub', $compiler->field('mrc_d071_sa'))
    ->map('num-upc', $compiler->field('mrc_d072_sa'))

    ->map('fldin_str_bid', $compiler->field('bid'))
    ->map('solr', $compiler->template('{}', false, false))
    //Missing: cdf
;

$expression = $builder
    //->field('series', 'asd"sd:')
    ->or()
        ->field('date', array('from' => 2000, 'to' => 3000))
        ->field('class', 'ciao')
        ->field('class', '830')
        ->field('publisher', 'mondadori')
        ->field('solr', 'sorti_date:["2000" TO "2010"]')
    ->getExpression();

echo '<pre>';
echo $compiler->compile($expression);;

var_dump(microtime(true) - $start);