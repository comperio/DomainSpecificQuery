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


class SimpleUrlParser extends UrlParser
{
    /**
     * {@inheritdoc}
     * @param array $array
     * @return array|mixed
     */
    public function normalize(array $array)
    {
        $normalized = array(
            array('and', $and = array()),
            array('not', $not = array()),
        );

        foreach ($array as $key => $value) {
            list($op, $field) = $this->getOpAndFieldname($key);
            $this->addField($field, $value, $op, $normalized);
        }

        return $normalized;
    }

    /**
     * Return ['and', 'field'] if $field == 'field'
     * and ['not', 'field'] if $field == '-field'
     *
     * @param string $field
     * @return array
     */
    private function getOpAndFieldname($field)
    {
        if (substr($field, 0, 1) == '-')
            return array('not', substr($field, 1));

        return array('and', $field);
    }

    /**
     * Add a field to the passed normalized array
     *
     * @param string $field
     * @param string $value
     * @param string $op
     * @param array $normalized
     * @return $this
     */
    private function addField($field, $value, $op, array &$normalized)
    {
        $index = $op == 'and' ? 0 : 1;
        list($fieldName, $fieldIndex) = $this->fieldAndIndex($field);

        $normalized[$index][1][] = array($fieldName, $value);

        return $this;
    }
} 