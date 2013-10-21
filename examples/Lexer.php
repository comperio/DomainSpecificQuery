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

$lexer = new \DSQ\Language\Lexer();
$grammar = new DSQ\Language\Grammar();
$parser = new \Dissect\Parser\LALR1\Parser($grammar);
$compiler = new \DSQ\Compiler\Label\LabelCompiler();
//$query = 'campo:(and) and io:(non so che dire) OR (boh:"stringa io qui: metto (tutto)" AND field:stringa\:con\ escaping )';

$query = 'b = (c = d g = (h:i l:m     d: asdasd r:(0:sdsd sdsd:    asdasd\ asdasd)))';
$query = 'title = (promessi sposi) AND autha = (m\anzoni \\\\( alessandro a b\=) OR asdasd = giulio';
$query = 'NOT (year = (from = 2000 to = 2010) OR year != 1923)';
$query = 'year > 2010 OR year <= 1900';
$query = '(campo IN (a, b\ asdsd\(   ,s, "ciao,()") AND foo=bar\( AND bez NOT IN (brutta,cattiva)) OR NOT (cool  = ha AND boh=bah OR doh=dah)';
//$query = 'foo = (a = b, c = d)';
$query = 'foo NOT IN (sd, sd\,sd, sdsd, "adsa,sdads", \(\), (ciao a tutti), asdasdasd, asdsd, (string con spazi senza problemi))';

$time = microtime(true);
$stream = $lexer->lex($query);
var_dump($query);
var_dump($stream);
$parsed = $parser->parse($stream);
var_dump(microtime(true) - $time);

var_dump($parsed);
echo '<pre>', (string) $compiler->compile($parsed);
