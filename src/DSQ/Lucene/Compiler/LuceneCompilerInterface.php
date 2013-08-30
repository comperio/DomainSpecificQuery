<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Lucene\Compiler;


use DSQ\Compiler\Compiler;

/**
 * Compilers that implements this interface are meant to compile to LuceneExpressions
 * @package DSQ\Lucene\Compiler
 */
interface LuceneCompilerInterface extends Compiler
{
} 