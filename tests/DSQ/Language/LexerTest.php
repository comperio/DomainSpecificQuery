<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Language;


use Dissect\Lexer\CommonToken;
use Dissect\Lexer\TokenStream\ArrayTokenStream;
use DSQ\Language\Lexer;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    public function testOutsideState()
    {
        $lexer = new Lexer();

        //Simplest expression
        $this->assertEquals(
            $this->tokenStream('foo', 'STRING', '=', 'FIELD_SEP', 'bar', 'STRING'),
            $lexer->lex('   foo  =   bar ')
        );

        //Simplest expression with different operators
        $this->assertEquals(
            $this->tokenStream('foo', 'STRING', '!=', 'FIELD_NOT_SEP', 'bar', 'STRING'),
            $lexer->lex('   foo  !=   bar ')
        );
        $this->assertEquals(
            $this->tokenStream('foo', 'STRING', '>=', '>=', 'bar', 'STRING'),
            $lexer->lex('   foo  >=   bar ')
        );

        //Escaped strings
        $this->assertEquals(
            $this->tokenStream('foo\=foo', 'STRING', '=', 'FIELD_SEP', '\(ah\)\ \=\"', 'STRING'),
            $lexer->lex('foo\=foo = \(ah\)\ \=\"')
        );
        $this->assertEquals(
            $this->tokenStream('foo', 'STRING', '=', 'FIELD_SEP', '"ahah mah\""', 'STRING_DOUBLEQUOTE_ENCAPSED'),
            $lexer->lex('foo="ahah mah\""')
        );
        $this->assertEquals(
            $this->tokenStream('foo', 'STRING', '=', 'FIELD_SEP', '(Hello World\(\))', 'STRING_PAREN_ENCAPSED'),
            $lexer->lex('foo=(Hello World\(\))')
        );

        //Operators
        //Possible tricky one
        $this->assertEquals(
            $this->tokenStream('foo', 'STRING', '=', 'FIELD_SEP', 'AND', 'STRING'),
            $lexer->lex('foo = AND')
        );

        // Nestend boolean expressions
        $this->assertEquals(
            $this->tokenStream('(', '(', 'a', 'STRING', '=', 'FIELD_SEP', 'b', 'STRING', 'AnD', 'OP_BOOLEAN',
                'c', 'STRING', '=', 'FIELD_SEP', 'd', 'STRING', ')', ')', 'OR', 'OP_BOOLEAN', 'e', 'STRING', '=', 'FIELD_SEP', 'f', 'STRING'),
            $lexer->lex('(a = b AnD c = d) OR e = f')
        );

        // FIeld IN (...)
        $this->assertEquals(
            $this->tokenStream('foo', 'STRING', 'IN', 'FIELD_IN', '(', '(', 'a', 'STRING', ',', ',', 'b', 'STRING',
                ',', ',', '"c"', 'STRING_DOUBLEQUOTE_ENCAPSED', ')', ')'),
            $lexer->lex('foo IN (a, b, "c")')
        );
    }

    public function testCompositeValues()
    {
        $lexer = new Lexer();

        //Simple composite expression
        $this->assertEquals(
            "a:STRING =:FIELD_SEP (:( b:STRING =:FIELD_SEP (:( c:STRING =:FIELD_SEP \"d\":STRING_DOUBLEQUOTE_ENCAPSED".
            " ,:, e:STRING =:FIELD_SEP (f g):STRING_PAREN_ENCAPSED ):) ):) :\$eof ",
            $this->prettyStream($lexer->lex('a = (b = (c = "d", e = (f g)))'))
        );
    }

    private function tokenStream()
    {
        $tokens = array();
        $args = func_get_args();

        for ($i = 0; $i < count($args); $i++) {
            $value = $args[$i];
            $type = $args[++$i];
            $tokens[] = new CommonToken($type, $value, 1);
        }

        $tokens[] = new CommonToken('$eof', '', 1);

        return new ArrayTokenStream($tokens);
    }

    private function prettyStream(ArrayTokenStream $stream)
    {
        $prettyStream = '';
        foreach ($stream->getIterator() as $token) {
            $prettyStream .= "{$token->getValue()}:{$token->getType()} ";
        }

        return $prettyStream;
    }
}
 