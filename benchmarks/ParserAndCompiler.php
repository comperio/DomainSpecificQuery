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

use DSQ\Comperio\Parser\SimpleUrlParser;

echo '<h4>Number of iterations:', $iterations = 90, '</h4>';

function printData($time, $title)
{
    global $iterations;

    echo "<b>$title</b>", '<br>';
    echo "Total time: ", $time, '<br>';
    echo "Average time: ", $time / $iterations, '<br>';
}
$parser = new SimpleUrlParser;
$compiler = new \DSQ\Comperio\Compiler\SimpleUrlCompiler();
$query = array(
    'foo_1' => 'bar',
    'foo_2' => 'bag',
    '-bar' => 'ah',
    '-bug_1' => 'doh',
    '-bug_2' => 'dag'
);

for ($i = 0; $i < $iterations; $i++) {
    $parser->parse($query);
}
printData( microtime(true) - $start, 'Simple Parser');

//Parse and then recompile
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $compiler->compile($parser->parse($query));
}
printData( microtime(true) - $start, 'Simple Parser and Simple Compiler');


$start = microtime(true);
$parser = new \DSQ\Comperio\Parser\AdvancedUrlParser();
$compiler = new \DSQ\Comperio\Compiler\AdvancedUrlCompiler();
$query = array(
    'op_1' => 'and',
    'op_2' => 'not',
    'field_1' => 'foo',
    'value_1' => 'bar',
    'lop_1' => '1',
    'field_2' => 'foo',
    'value_2' => 'bag',
    'lop_2' => '1',
    'field_3' => 'bar',
    'value_3' => 'ah',
    'lop_3' => '2',
    'field_4' => 'bug',
    'value_4' => 'doh',
    'lop_4' => '2',
    'field_5' => 'bug',
    'value_5' => 'dag',
    'lop_5' => '2',
);

for ($i = 0; $i < $iterations; $i++) {
    $parser->parse($query);
}

printData( microtime(true) - $start, 'Advanced Parser');

//Parse and then recompile
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $compiler->compile($parser->parse($query));
}
printData( microtime(true) - $start, 'Advanced Parser and advanced Compiler');