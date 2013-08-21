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

use DSQ\Comperio\Compiler\Map\SubjTypeMap;
use DSQ\Expression\BinaryExpression;
use DSQ\Lucene\Compiler\LuceneCompiler;
use DSQ\Expression\Builder\Builder;
use DSQ\Lucene\Compiler\Map\MapBuilder;

include '../vendor/autoload.php';
$start = microtime(true);
$compiler = new LuceneCompiler;
$builder = new Builder;
$m = new MapBuilder;

$compiler
    ->map('home-lib', $m->field('faceti_libvisi'))
    ->map('facets-biblevel-full', $m->field('facets_biblevel_full'))
    ->map('facets-biblevel-full', $m->field('facets_biblevel'))
    ->map('facets-subject', $m->field('facets_subject'))
    ->map('subject', $m->field('fldin_txt_subject'))
    ->map('subject-type', $m->field('mrc_d610_s9'))
    ->map('subj-and-type', new SubjTypeMap)
    ->map(
        'series', $m->combine(
            'or', $m->template(
                $seriesTpl
                        = '(mrc_d901_sb:"c" AND (mrc_d200_sa:{}^100 OR mrc_d200_sc:{}^1000 OR mrc_d200_sd:{}^1000 OR mrc_d200_se:{} OR mrc_d200_sh:{} OR mrc_d200_si:{})) OR (mrc_d225_sa:{} OR mrc_d410_st:{})'
            ), $m->template($seriesTpl, true)
        )
    )
    ->map('dewey', $m->field('facets_class'))
    ->map('fulltext-atc', $m->field('fldin_txt_fulltextattach'))
    ->map('classtxt', $m->field('fldin_txt_class'))
    ->map(
        'class', $m->regexps(
            array(
                '/^"?\d+(\.\d*)?\*?"?$/' => $compiler->getMap('*', 'dewey'),
                '/.*/' => $compiler->getMap('*', 'classtxt'),
            )
        )
    )
    ->map('facets-class-desc', $m->field('facets_class_desc'))
    ->map('facets-editore', $m->field('facets_publisher'))
    ->map(
        'publisher', $m->combine(
            'or', $m->field('mrc_d210_sa'), $m->field('mrc_d210_sb'), $m->field('mrc_d210_sc'),
            $m->field('fldint_txt_publisher')
        )
    )
    ->map(array('materiale', 'facets-materiale'), $m->conditional(array(
        array(
            function (BinaryExpression $expr) {
                $v = $expr->getRight()->getValue();
                return isset($v['bibtype']) && $v['bibtype'];
            },
            $m->template('mrc_d901_sa:{bibtype}')
        ),
        array(
            function () { return true; },
            $m->template('mrc_d901_sc:{bibtypefirst}')
        )
    )))
    ->map(
        'aut', $m->combine(
            'or', $m->field('mrc_d700_sa'), $m->field('mrc_d701_sa'), $m->field('mrc_d702_sa'),
            $m->field('mrc_d710_sa'), $m->field('mrc_d711_sa'), $m->field('mrc_d712_sa'),
            $m->field('mrc_d720_sa'), $m->field('mrc_d790_sa')
        )
    )
    ->map(
        'aut-role', $m->combine(
            'or', $m->field('mrc_d700_s4'), $m->field('mrc_d701_s4'), $m->field('mrc_d702_s4'),
            $m->field('mrc_d710_s4'), $m->field('mrc_d711_s4'), $m->field('mrc_d712_s4'),
            $m->field('mrc_d720_s4')
        )
    )
    ->map('facets-lang', $m->field('facets_lang'))
    ->map(array('facets-date', 'year'), $m->field('sorti_date'))
    ->map('facets-place', $m->field('facets_place'))
    ->map('facets-country', $m->field('facets_country'))
    ->map('facets-owner', $m->field('facets_owner'))
    ->map('facets-printer', $m->field('facets_printer'))
    ->map('facets-author', $m->field('facets_author'))
    ->map('facets-author-main', $m->field('facets_author_main'))
    ->map('id-auth', $m->subval($m->field('fldin_str_authid')))
    ->map('id-subj', $m->subval($m->field('fldin_str_subj')))
    ->map('id-work', $m->subval($m->field('mrc_d500_s3')))
    ->map('id-marca', $m->subval($m->field('mrc_d921_s3')))
    ->map('biblevel', $m->field('mrc_d901_sb'))
    ->map('bibtype', $m->field('mrc_d901_sa'))
    ->map('target', $m->field('mrc_cdf'))
    //missing: facets-target search field
    ->map('pub-name', $m->field('mrc_d210_sc'))
    ->map('pub-place', $m->field('mrc_d210_sa'))
    ->map('collocation', $m->field('mrc_d950_sf'))
    ->map('language', $m->field('facets_lang'))
    ->map('autha', $m->template('fldin_txt_author_main:{}^1000 OR fldin_txt_author:{}^10'))
    ->map('owner', $m->field('fldin_txt_owner'))
    ->map('printer', $m->field('fldin_txt_printer'))
    ->map(
        'title', $m->template(<<<TPL
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
        'place', $m->combine(
            'or', $m->field('mrc_d620_sa'), $m->field('mrc_d620_sb'), $m->field('mrc_d620_sc'),
            $m->field('mrc_d620_sd')
        )
    )
    ->map('year', $m->range('sorti_date'))
    ->map('segnatura', $m->field('fldis_str_collocation'))
    ->map('tid', $m->field('id'))
    ->map('q', $m->term())
    //Missing: libarea (LibraryAreaSearchField
    ->map('collection', $m->field('collection', '*', 'collection'))
    //Missing: Facets eta...
    //Missing: loanable
    ->map(
        'ean', $m->combine(
            'or', $m->field('mrc_d073_sa'), $m->field('mrc_d010_sa'), $m->field('mrc_d011_sa'),
            $m->field('mrc_d012_sa'), $m->field('mrc_d013_sa'), $m->field('mrc_d014_sa'),
            $m->field('mrc_d015_sa'), $m->field('mrc_d016_sa'), $m->field('mrc_d017_sa')
        )
    )
    //Missing: standard-number: multiplesolrsearchfield
    ->map('num-ean', $m->field('mrc_d073_sa'))
    ->map('num-isbn', $m->field('mrc_d010_sa'))
    ->map('num-issn', $m->field('mrc_d011_sa'))
    ->map('num-fingerprint', $m->field('mrc_d012_sa'))
    ->map('num-ismn', $m->field('mrc_d013_sa'))
    ->map('num-article', $m->field('mrc_d014_sa'))
    ->map('num-isrn', $m->field('mrc_d015_sa'))
    ->map('num-isrc', $m->field('mrc_d016_sa'))
    ->map('num-other', $m->field('mrc_d017_sa'))
    ->map('num-natbib', $m->field('mrc_d020_sa'))
    ->map('num-depleg', $m->field('mrc_d021_sa'))
    ->map('num-gov', $m->field('mrc_d022_sa'))
    ->map('num-coden', $m->field('mrc_d040_sa'))
    ->map('num-pub', $m->field('mrc_d071_sa'))
    ->map('num-upc', $m->field('mrc_d072_sa'))

    ->map('fldin_str_bid', $m->field('bid'))
    ->map('solr', $m->template('{}', false, false))
    //Missing: cdf
;

$expression = $builder
    //->field('series', 'asd"sd:')
    ->or()
        ->field('year', array('from' => 2000, 'to' => 3000))
        ->field('class', 'ciao')
        ->field('class', '830')
        ->field('publisher', 'mondadori')
        ->field('solr', 'sorti_date:["2000" TO "2010"]')
        //->field('subj-and-type', array('s' => 'ragazzi', 't' => 'firenze'))
        ->field('materiale', array('bibtype' => 'ah'))
        ->field('materiale', array('bibtypefirst' => 'boh'))
        ->field('id-subj', array('value' => 'ciao', 'name' => 'boh'))
        ->field('id-subj', 'scalar')
    ->getExpression();


echo $compiler->compile($expression);;

var_dump(microtime(true) - $start);