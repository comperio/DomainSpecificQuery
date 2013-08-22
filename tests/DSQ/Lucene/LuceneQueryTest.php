<?php
/*
 * This file is part of DomainSpecificQuery.
 *
 * (c) 2013 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DSQ\Test\Lucene;

use DSQ\Lucene\LuceneQuery;
use DSQ\Lucene\MatchAllExpression;

/**
 * Unit tests for class LuceneQuery
 */
class LuceneQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LuceneQuery
     */
    protected $query;

    public function setUp()
    {
        $this->query = new LuceneQuery;
    }

    public function testConstructorSetMainQuery()
    {
        $query = new LuceneQuery('foo');

        $this->assertEquals('foo', $query->getMainQuery());
    }

    public function testSetAndGetMainQuery()
    {
        $query = new LuceneQuery;

        $query->setMainQuery('bar');

        $this->assertEquals('bar', $query->getMainQuery());
    }

    public function testConstructorWithoutArgumentsSetMainQueryToAllQuery()
    {
        $query = new LuceneQuery;

        $this->assertEquals(LuceneQuery::ALLQUERY, $query->getMainQuery());
    }

    public function testSetAndGetFilterQueries()
    {
        $query = new LuceneQuery;

        $query->setFilterQueries($filters = array('foo', 'bar'));

        $this->assertEquals($filters, $query->getFilterQueries());
    }

    public function testAddFilterQuery()
    {
        $query = new LuceneQuery();

        $query
            ->addFilterQuery('foo')
            ->addFilterQuery('bar')
            ->addFilterQuery('baz')
        ;

        $this->assertEquals(array('foo', 'bar', 'baz'), $query->getFilterQueries());
    }

    public function testHasTrivialMainQuery()
    {
        $query = new LuceneQuery(LuceneQuery::ALLQUERY);
        $this->assertTrue($query->hasTrivialMainQuery());

        $query->setMainQuery(new MatchAllExpression);
        $this->assertTrue($query->hasTrivialMainQuery());

        $query->setMainQuery('foo');
        $this->assertFalse($query->hasTrivialMainQuery());
    }


}