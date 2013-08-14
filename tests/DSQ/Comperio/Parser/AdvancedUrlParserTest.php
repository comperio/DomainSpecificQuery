<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

use DSQ\Comperio\Parser\AdvancedUrlParser;

class AdvancedParserTest extends PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $parser = new AdvancedUrlParser();

        $ary = array(
            'op_1' => 'and',
            'op_2' => 'or',

            'field_1' => 'foo',
            'value_1' => 'voo',
            'lop_1' => '1',

            'field_2' => 'foo2',
            'value_2' => 'voo2',
            'lop_2' => '2',

            'field_3' => 'foo3',
            'value_3' => 'voo3',
            'lop_3' => '1',

            'field_4' => 'foo4',
            'value_4' => 'voo4',
            'lop_4' => '2',
        );

        $this->assertEquals(
            array(
                array('and', array(array('foo', 'voo'), array('foo3', 'voo3'))),
                array('or', array(array('foo2', 'voo2'), array('foo4', 'voo4'))),
            ),
            $parser->normalize($ary)
        );
    }
}
 