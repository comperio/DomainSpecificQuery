<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Lucene;

use DSQ\Expression\BasicExpression;

class BasicLuceneExpression extends BasicExpression implements LuceneExpression
{
    /**
     * Escape a string to be a suitable lucene value
     *
     * @param string $string
     * @return mixed
     */
    public static function escape($string)
    {
        //list taken from http://lucene.apache.org/java/docs/queryparsersyntax.html#Escaping%20Special%20Characters
        //Removed *, ? and ^.
        return preg_replace('/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|~|:|\\\)/', '\\\$1', $string);
    }

    /**
     * Escape a string for quoted values
     *
     * @param string $string
     * @return mixed
     */
    public static function escape_phrase($string)
    {
        return preg_replace('/("|\\\)/', '\\\$1', $string);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}