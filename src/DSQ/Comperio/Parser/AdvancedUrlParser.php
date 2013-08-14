<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Comperio\Parser;


/**
 * Class AdvancedUrlParser
 * This reflects how DiscoveryNG advanced search parses search uris.
 * The expected format of the query string is
 * <code>
 * [
 *      op_1 => o_1
 *      ...
 *      op_m => o_m
 *      field_1 => f_1
 *      value_1 => v_1
 *      lop_1 => l_1 // l_x must be in {1,...,m}
 *      ...
 *      field_n => f_n
 *      value_n => v_n
 *      lop_n => l_n // l_x must be in {1,...,m}
 * ]
 * </code>
 * Here "op_i" are the operators of the first level subtrees,
 * field_j, value_j are the field name and field value respectively,
 * lop_j is the index of the subtree the field belongs to.
 *
 * @package DSQ\Comperio\Parser
 */
class AdvancedUrlParser extends UrlParser
{
    /**
     * {@inheritdoc}
     * @param array $array
     * @throws MalformedUrlException
     * @return array|mixed
     */
    public function normalize(array $array)
    {
        $normalized = array();
        list($operators, $fields) = $this->parseQueryArray($array);

        foreach ($fields as $fieldIndex => $triple) {
            if (count($triple) < 3)
                throw new MalformedUrlException("Field name, value or operator index is missing for field at index $fieldIndex");
            list($field, $value, $opIndex) = $triple;
            if (!isset($operators[$opIndex]))
                throw new MalformedUrlException("There is no operator defined for the index $opIndex");
            if (!isset($normalized[$opIndex]))
                $normalized[$opIndex] = array($operators[$opIndex], array());
            $normalized[$opIndex][1][] = array($field, $value);
        }

        return array_values($normalized);
    }


    /**
     * Parse the query array and returns two arrays.
     * The first is for operators, i.e. the first level query subtrees,
     * and it is in the form
     * <code>
     * [
     *      opindex => opvalue,
     *      ...
     * ]
     * </code>
     * The second is for fields, and it's in the form
     * <code>
     * [
     *      fieldindex => [fieldname, value, opindex],
     *      ...
     * ]
     * </code>
     * @param array $array
     * @return array[]
     */
    private function parseQueryArray(array $array)
    {
        $operators = array();
        $fields = array();

        foreach ($array as $indexedKey => $value) {
            list($key, $index) = $this->fieldAndIndex($indexedKey);

            if ($key == 'op') {
                $operators[(int) $index] = $value;
            } else {
                //field case
                $fieldIndex = 0;
                switch ($key) {
                    case 'value':
                        $fieldIndex = 1;
                        break;
                    case 'lop':
                        $fieldIndex = 2;
                        $value = (int) $value;
                }
                $fields[(int) $index][$fieldIndex] = $value;
            }
        }

        return array($operators, $fields);
    }
} 