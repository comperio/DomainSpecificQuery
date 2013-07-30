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


class LuceneQuery
{
    const ALLQUERY = '*:*';

    /**
     * @var string
     */
    private $mainQuery;

    /**
     * @var string[]
     */
    private $filterQueries = array();

    /**
     * @param string $mainQuery
     */
    public function __construct($mainQuery = self::ALLQUERY)
    {
        $this->mainQuery = $mainQuery;
    }

    /**
     * Set FilterQueries
     *
     * @param string[] $filterQueries
     *
     * @return $this The current instance
     */
    public function setFilterQueries($filterQueries = array())
    {
        $this->filterQueries = $filterQueries;

        return $this;
    }

     /* Get FilterQueries
     *
     * @return string[]
     */
    public function getFilterQueries()
    {
        return $this->filterQueries;
    }

    /**
     * @param string $filterQuery
     * @return $this
     */
    public function addFilterQuery($filterQuery)
    {
        $this->filterQueries[] = $filterQuery;

        return $this;
    }

    /**
     * Set MainQuery
     *
     * @param string $mainQuery
     *
     * @return $this The current instance
     */
    public function setMainQuery($mainQuery)
    {
        $this->mainQuery = $mainQuery;

        return $this;
    }

    /**
     * Get MainQuery
     *
     * @return string
     */
    public function getMainQuery()
    {
        return $this->mainQuery;
    }
}