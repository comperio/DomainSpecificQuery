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
    const EMPTYQUERY = "*:* NOT *:*";

    /**
     * @var string
     */
    private $mainQuery;

    /**
     * @var string[]
     */
    private $filterQueries = array();

    /**
     * @param mixed $mainQuery
     */
    public function __construct($mainQuery = self::ALLQUERY)
    {
        $this->mainQuery = $mainQuery;
    }

    /**
     * Set FilterQueries
     *
     * @param mixed[] $filterQueries
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
     * @return mixed[]
     */
    public function getFilterQueries()
    {
        return $this->filterQueries;
    }

    /**
     * @param mixed $filterQuery
     * @return $this
     */
    public function addFilterQuery($filterQuery)
    {
        $this->filterQueries[] = $filterQuery;

        return $this;
    }

    /**
     * @param array $filterQueries An array of filter queries
     * @return $this
     */
    public function addFilterQueries(array $filterQueries)
    {
        foreach ($filterQueries as $filterQuery) {
            $this->addFilterQuery($filterQuery);
        }

        return $this;
    }

    /**
     * Set MainQuery
     *
     * @param mixed $mainQuery
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
     * @return mixed
     */
    public function getMainQuery()
    {
        return $this->mainQuery;
    }

    /**
     * @return bool
     */
    public function hasTrivialMainQuery()
    {
        $main = $this->getMainQuery();
        return
            $main instanceof MatchAllExpression
            || $main === self::ALLQUERY
        ;
    }

    /**
     * Convert all expressions to strings.
     *
     * @return $this
     */
    public function convertExpressionsToStrings()
    {
        $this->setMainQuery((string) $this->getMainQuery());

        $this->setFilterQueries(array_map(
            function($expr) { return (string) $expr; },
            $this->getFilterQueries()
        ));

        return $this;
    }
}