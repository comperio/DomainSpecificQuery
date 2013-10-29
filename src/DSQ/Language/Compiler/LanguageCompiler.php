<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Language\Compiler;

use DSQ\Compiler\MatcherCompiler;
use DSQ\Expression\FieldExpression;
use DSQ\Expression\TreeExpression;
use DSQ\Language\Lexer;

/**
 * Class LanguageCompiler
 *
 * Convert an expression to a DSL string
 *
 * @package DSQ\Language\Compiler
 */
class LanguageCompiler extends MatcherCompiler
{
    public function __construct()
    {
        parent::__construct();

        $this->mapByClass('DSQ\Expression\FieldExpression', $this->fieldExpression());
    }

    private function fieldExpression()
    {
        return function(FieldExpression $expr, LanguageCompiler $compiler)
        {
            $field = $compiler->identifier($expr->getField());
            $value = $compiler->value($expr->getValue());

            return "$field {$expr->getOp()} $value";
        };
    }

    private function treeExpression()
    {
        return function(TreeExpression $expr, LanguageCompiler $compiler)
        {

        };
    }

    /**
     * Compile a value
     *
     * @param string $value
     * @return string
     */
    public function value($value)
    {
        //terminal case
        if (!is_array($value)) {
            if (strpbrk($value, Lexer::ESCAPED_STRING) !== false) {
                if (!(substr($value, 0, 1) == '"' && substr($value, -1) == '"')) {
                    return '(' . addcslashes($value, Lexer::ESCAPED_STRING_PAREN_ENCAPSED) . ')';
                }
            }
            return $value;
        }

        //composite value case
        $pairs = array();
        foreach ($value as $key => $subval) {
            $pairs[] = "{$this->identifier($key)} = {$this->value($subval)}";
        }

        return '(' . implode(', ', $pairs) . ')';
    }

    /**
     * @param string $string
     * @return string
     */
    public function identifier($string)
    {
        return addcslashes($string, Lexer::ESCAPED_STRING);
    }
} 