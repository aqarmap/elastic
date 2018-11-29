<?php

namespace Tests\Buliders;

use Elastica\Query\Range;
use Elastica\Query\Terms;
use ElasticRepository\Factory\QueryFactory;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: hafez
 * Date: 29/11/18
 * Time: 16:18
 */
class QueryFactoryTest extends TestCase
{
    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    public function setUp()
    {
        $this->queryFactory = new QueryFactory();
    }

    public function test_terms_return_obj()
    {
        $queryObj = $this->queryFactory->setTerms('', []);

        $this->assertInstanceOf(Terms::class, $queryObj);
    }

    public function test_range_return_obj()
    {
        $queryObj = $this->queryFactory->setRange('', []);

        $this->assertInstanceOf(Range::class, $queryObj);
    }
}