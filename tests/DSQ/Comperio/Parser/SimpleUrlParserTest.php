<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

use DSQ\Comperio\Parser\SimpleUrlParser;
class SimpleUrlParserTest extends PHPUnit_Framework_TestCase
{

    public function testNormalizeWithOnlyOneValuePerType()
    {
        $parser = new SimpleUrlParser;

        $ary = array(
            'foo' => 'bar',
            '-foo' => 'baz',
        );

        $this->assertEquals(
            array(
                array('and', array(array('foo', 'bar'))),
                array('not', array(array('foo', 'baz'))),
            ),
            $parser->normalize($ary)
        );
    }

    public function testNormalize()
    {
        $parser = new SimpleUrlParser;

        $ary = array(
            'foo_1' => 'bar',
            'foo_2' => 'bug',
            '-moo_1' => 'baz',
            '-moo_2' => 'fez',
        );

        $this->assertEquals(
            array(
                array('and', array(array('foo', 'bar'), array('foo', 'bug'))),
                array('not', array(array('moo', 'baz'), array('moo', 'fez'))),
            ),
            $parser->normalize($ary)
        );
    }
}
 