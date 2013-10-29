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
            $field = addcslashes($expr->getField(), Lexer::ESCAPED_STRING);
            $value = $expr->getValue();
            if (strpbrk($value, Lexer::ESCAPED_STRING) !== false) {
                if (!(substr($value, 0, 1) == '"' && substr($value, -1) == '"')) {
                    $value = '(' . addcslashes($value, Lexer::ESCAPED_STRING_PAREN_ENCAPSED) . ')';
                }
            }

            return "$field {$expr->getOp()} $value";
        };
    }
} 