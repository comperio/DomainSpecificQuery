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

use Dissect\Lexer\CommonToken;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\FieldExpression;
use DSQ\Expression\TreeExpression;
use DSQ\Lucene\RangeExpression;

/**
 * The Grammar definition of our DSL
 * @package DSQ\Language
 */
class Grammar extends \Dissect\Parser\Grammar
{
    /**
     * Grammar definition
     */
    public function __construct()
    {
        $doubleQuoteCall = function(CommonToken $token) {
            $str = substr($token->getValue(), 1, -1);
            $unescapeChars = str_replace('"', '', Lexer::ESCAPED_STRING_DOUBLEQUOTE_ENCAPSED);
            return '"' . $this->unescape($str, $unescapeChars) . '"';
        };

        $notCall = function ($expr) {
            $not = new TreeExpression('NOT');
            $not->addChild($expr);
            return $not;
        };

        $listCall = function($field, array $list) {
            $tree = new TreeExpression('OR');
            foreach ($list as $value) {
                $tree->addChild(new FieldExpression($field, $value));
            }
            return $tree;
        };

        $identity = function($v) { return $v; };

        $comparison = function($field, CommonToken $op, $value) {
            return new BinaryExpression($op->getValue(), $field, $value);
        };

        // A valid DSL expression
        $this('Expr')
            ->is('(', 'ExprSpan', ')')
            ->call(function ($_, $expr, $__) { return $expr; })
            ->is('ExprField')
            ->call(function ($expr) { return $expr; })
            ->is('OP_NOT', 'Expr')
            ->call(function($_, $expr) use ($notCall) { return $notCall($expr); })
        ;

        // This is for the outmost expression: I want to allow span expressions without parenthesis
        // if they are the outmost, i.e.
        // "foo:bar AND baz = bar" in addition to "(foo = bar AND baz = bar)"
        $this('InitialExpr')
            ->is('ExprSpan')
        ;

        // A list of expressions intermixed with boolean operators
        // e.g "foo = bar AND bim = bum OR baz = bar"
        $this('ExprSpan')
            ->is('Expr')
            ->call(function($expr){
                return $expr;
            })
            ->is('ExprSpan', 'OP_BOOLEAN', 'Expr')
            ->call(function($left, CommonToken $op, $right) {
                $operator = strtoupper($op->getValue());
                if ($left instanceof TreeExpression && $operator == $left->getValue()) {
                    $tree = $left->addChild($right);
                } else {
                    $tree = new TreeExpression($operator);
                    $tree->addChild($left)->addChild($right);
                }
                return $tree;
            })
        ;

        // A field expression: a thing like "foo = bar"
        $this('ExprField')
            ->is('FieldName', 'FIELD_SEP', 'Value')
            ->call(function($field, $_, $value) {
                return new FieldExpression($field, $value);
            })
            ->is('FieldName', 'FIELD_NOT_SEP', 'Value')
            ->call(function($field, $_, $value) use ($notCall) {
                return $notCall(new FieldExpression($field, $value));
            })
            ->is('FieldName', '>', 'Value')->call($comparison)
            ->is('FieldName', '>=', 'Value')->call($comparison)
            ->is('FieldName', '<', 'Value')->call($comparison)
            ->is('FieldName', '<=', 'Value')->call($comparison)
            ->is('FieldName', 'FIELD_IN', '(', 'ValueInList+', ')')
            ->call(function($field, $_, $__, $list, $___) use ($listCall) {
                return $listCall($field, $list);
            })
            ->is('FieldName', 'OP_NOT', 'FIELD_IN', '(', 'ValueInList+', ')')
            ->call(function($field, $_, $__, $___, $list, $____) use ($listCall, $notCall) {
                return $notCall($listCall($field, $list));
            })
        ;

        // The field identifier
        // e.g: foo in "foo = bar"
        $this('FieldName')
            ->is('STRING')
            ->call(function(CommonToken $token) {
                return $token->getValue();
            });

        // Value: what can be inserted as a field value.
        // e.g.: "bar" in "foo = bar"
        $this('Value')
            ->is('STRING')
            ->call(function(CommonToken $token) {
                return $this->unescape($token->getValue(), Lexer::ESCAPED_STRING);
            })
            ->is('STRING_PAREN_ENCAPSED')
            ->call(function(CommonToken $token) {
                $str = substr($token->getValue(), 1, -1);
                return $this->unescape($str, Lexer::ESCAPED_STRING_PAREN_ENCAPSED);
            })
            ->is('STRING_DOUBLEQUOTE_ENCAPSED')
            ->call($doubleQuoteCall)
            ->is('(', 'KeyValuePair+', ')')
            ->call(function($_, $ary, $__) { return $ary; });


        // A list of key-value pairs
        // e.g.: "foo = bar baz = bag car = lag"
        $this('KeyValuePair+')
            ->is('KeyValuePair')->call($identity)
            ->is('KeyValuePair+', ',', 'KeyValuePair')
            ->call(function($composite, $_, $pair) { return $composite + $pair; });

        // A single key-value pair in a composite value
        // e.g.: "foo = bar" in "field = (foo = bar goo = car)"
        $this('KeyValuePair')
            ->is('STRING', 'FIELD_SEP', 'Value')
            ->call(function ($key, $_, $v) { return array($key->getValue() => $v); })
        ;

        // A list of comma separated values
        // e.g "bar, baz" in "foo IN (bar, baz)"
        $this('ValueInList+')
            ->is('Value')->call(function($value) { return array($value); })
            ->is('ValueInList+', ',', 'Value')
            ->call(function($list, $_, $value) { $list[] = $value; return $list; });

        $this->start('InitialExpr');
    }

    /**
     * Do unescaping
     *
     * @param string $string
     * @param string $chars
     * @param string $escapeChar
     * @return string
     */
    private function unescape($string, $chars, $escapeChar = "\\")
    {
        if (!$chars)
            return $string;

        $escapeChar = preg_quote($escapeChar);
        $chars = preg_quote($chars);
        return preg_replace("/$escapeChar([$chars])/", "$1", $string);
    }
}