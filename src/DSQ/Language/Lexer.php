<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Language;


use Dissect\Lexer\StatefulLexer;

/**
 * Class Lexer
 * @package DSQ\Language
 */
class Lexer extends StatefulLexer
{
    const FIELD_SEP = '=';
    const FIELD_NOT_SEP = '!=';

    const ESCAPED_STRING_PAREN_ENCAPSED = "()=";
    const ESCAPED_STRING_DOUBLEQUOTE_ENCAPSED = '"';
    const ESCAPED_STRING = '()"= ,';

    const WHITESPACES = " \r\n\t";

    /**
     * Lexer definition is here
     */
    public function __construct()
    {
        //outside a field definition
        $this->state('outside')
            ->regex('WHITESPACE', $this->whitespaces())
            ->token('(')->token(')')
            ->token('>')->token('<')->token('>=')->token('<=')
            ->regex('OP_BOOLEAN', '/^(and|or)/i')
            ->regex('OP_NOT', '/^not/i')
            ->regex('FIELD_IN', '/^in/i')->action('fieldin')
            ->regex('STRING', $this->escapedSequenceRegex(static::ESCAPED_STRING, '+'))
            ->token('FIELD_SEP', static::FIELD_SEP)->action('value')
            ->token('FIELD_NOT_SEP', static::FIELD_NOT_SEP)->action('value')
            ->skip('WHITESPACE');

        //inside a field value
        $this->state('value')
            ->regex('WHITESPACE', $this->whitespaces())
            ->token('(')->action('fieldcompositevalue')
            ->token(')')->action(static::POP_STATE)
            ->regex('STRING_PAREN_ENCAPSED', "/^\\({$this->escapedChar(static::ESCAPED_STRING_PAREN_ENCAPSED)}*\\)/")
            ->action(StatefulLexer::POP_STATE)
            ->regex('STRING_DOUBLEQUOTE_ENCAPSED', "/^\"{$this->escapedChar(static::ESCAPED_STRING_DOUBLEQUOTE_ENCAPSED)}*\"/")
            ->action(StatefulLexer::POP_STATE)
            ->regex('STRING', $this->escapedSequenceRegex(static::ESCAPED_STRING, '+'))->action(static::POP_STATE)
            ->skip('WHITESPACE', 'EMPTY');

        //inside a composite field value
        $this->state('fieldcompositevalue')
            ->regex('WHITESPACE', $this->whitespaces())
            ->token(',', ',')
            ->token('EMPTY', '')->action(static::POP_STATE) //Pop when no more tokens are recognized
            ->token('FIELD_SEP', static::FIELD_SEP)->action('value')
            ->regex('STRING', $this->escapedSequenceRegex(static::ESCAPED_STRING, '+'))
            ->skip('WHITESPACE')
        ;

        //inside a "FIELD IN ..." expression
        $this->state('fieldin')
            ->regex('WHITESPACE', $this->whitespaces())
            ->token(',')->action('value')
            ->token('(')->action('value')
            ->token(')')->action(static::POP_STATE)
            //->regex('STRING', $this->escapedSequenceRegex(static::ESCAPED_STRING, '+'))
            //->regex('STRING_DOUBLEQUOTE_ENCAPSED', "/^\"{$this->escapedChar(static::ESCAPED_STRING_DOUBLEQUOTE_ENCAPSED)}*\"/")
            ->skip('WHITESPACE');

        $this->start('outside');
    }

    /**
     * A regular expression that match all given escaped characters
     * or all non-to-be-escaped ones
     *
     * @param $charsToEscape
     * @param string $escapeChar
     * @return string
     */
    private function escapedChar($charsToEscape, $escapeChar = '\\')
    {
        $escapeChar = preg_quote($escapeChar);

        return "(?:{$escapeChar}[{$charsToEscape}]|[^$charsToEscape])";
    }

    /**
     * A sequence of escaped chars
     *
     * @param $charsToEscape
     * @param string $quantifier
     * @param string $escapeChar
     * @return string
     */
    private function escapedSequenceRegex($charsToEscape, $quantifier = '*', $escapeChar = '\\')
    {
        return "/^{$this->escapedChar($charsToEscape, $escapeChar)}$quantifier/";
    }

    /**
     * Regex for ignored whitespaces
     * @return string
     */
    private function whitespaces()
    {
        return "/^[" . static::WHITESPACES . "]+/";
    }
}