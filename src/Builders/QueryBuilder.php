<?php

namespace ElasticRepository\Builders;

use Elastica\Query\Match;
use Elastica\Query\SimpleQueryString;
use Elastica\Query\QueryString;
use ElasticRepository\Contracts\SearchContract;
use ElasticRepository\Contracts\SearchInRangeContract;
use Elastica\Query\BoolQuery;
use Elastica\Query\Exists;
use Elastica\Query\Range;
use Elastica\Query\Term;
use Elastica\Query\Terms;
use ElasticRepository\Factory\QueryFactory;
use Elastica\Query\GeoDistance;

class QueryBuilder implements SearchInRangeContract, SearchContract
{
    /**
     * The query where clauses.
     *
     * @var array
     */
    protected $where = [];

    /**
     * @var array $exist
     */
    protected $exist = [];

    /**  @var array $notExists */
    protected $notExists = [];

    /**
     * @var array $whereTerms
     */
    protected $whereTerms = [];

    /**
     * The query whereNot clauses.
     *
     * @var array
     */
    protected $whereNot = [];

    /**
     * The query whereNotIn clauses.
     *
     * @var array
     */
    protected $whereNotIn = [];

    /**
     * The query whereOr clauses.
     *
     * @var array
     */
    protected $whereOr = [];

    /**
     * The query in range clauses.
     *
     * @var array
     */
    protected $inRange = [];

    /**
     * The query not in range clauses.
     *
     * @var array
     */
    protected $notInRange = [];

    /**@var array $match */
    protected $match = [];

    /**@var array $mismatch */
    protected $mismatch = [];

    /**@var array $geoDistance */
    protected $geoDistance = [];

    /**
     * @var array $simpleQueryString
     */
    protected $simpleQueryString = [];

    /**
     * @var array $queryString
     */
    protected $queryString = [];

    /**
     * @var array $boolShould
     */
    protected $boolShould = [];

    /**
     * @var BoolQuery
     */
    protected $filter;

    /**
     * @var BoolQuery
     */
    protected $query;

    /**
     * @var array $boolShould
     */
    protected $queryFactory;

    public function __construct()
    {
        $this->query  = new BoolQuery();
        $this->filter = new BoolQuery();
        $this->queryFactory = new QueryFactory();
    }

    /**
     * Add a "Where" clause to the query.
     * @param $attribute
     * @param null $value
     * @param float $boost
     * @return $this
     */
    public function where($attribute, $value = null, $boost = 1.0)
    {
        $this->where[] = [$attribute, $value, $boost ?: 1.0];
        return $this;
    }

    /**
     * Add a "Where Not" clause to the query.
     *
     * @param string $attribute
     * @param null $value
     * @param float $boost
     * @return $this
     */
    public function whereNot($attribute, $value = null, $boost = 1.0)
    {
        $this->whereNot[] = [$attribute, $value, $boost ?: 1.0];
        return $this;
    }

    /**
     * @param $attribute
     * @param null $value
     * @return $this
     */
    public function whereNotIn($attribute, $value = null)
    {
        $this->whereNotIn[] = [$attribute, $value];
        return $this;
    }

    /**
     * Add a "in range" clause to the query.
     *
     * @param string $attribute
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function inRange($attribute, $from = '', $to = '')
    {
        $this->inRange[] = [$attribute, $from, $to];

        return $this;
    }

    /**
     * Add a "not in range" clause to the query.
     *
     * @param string $attribute
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function notInRange($attribute, $from = '', $to = '')
    {
        $this->notInRange[] = [$attribute, $from, $to];

        return $this;
    }

    /**
     * add new terms to the main filter
     * @param $attribute
     * @param $value
     * @return $this
     */
    public function whereTerm($attribute, $value)
    {
        $this->whereTerms[] = [$attribute, $value];
        return $this;
    }

    /**
     * @param $terms
     * @return $this
     */
    public function whereOr($terms)
    {
        $this->whereOr = $terms;
        return $this;
    }

    /**
     * add exist constrains to the main filter
     * @param $attribute
     * @return $this
     */
    public function exist($attribute)
    {
        $this->exist[] = $attribute;

        return $this;
    }

    /**
     * add not exists constrains to the main filter
     * @param $attribute
     * @return $this
     */
    public function notExists($attribute)
    {
        $this->notExists[] = $attribute;

        return $this;
    }

    /**
     * match words to field
     * @param $attribute
     * @param $keyword
     * @return $this
     */
    public function match($attribute, $keyword)
    {
        $this->match[] = [$attribute, $keyword];

        return $this;
    }

    /**
     * mismatch words to field
     * @param $attribute
     * @param $keyword
     * @return $this
     */
    public function mismatch($attribute, $keyword)
    {
        $this->mismatch[] = [$attribute, $keyword];

        return $this;
    }

    /**
     * Geo distance
     * @param $key
     * @param $location
     * @param $distance
     * @return $this
     */
    public function geoDistance($key, $location, $distance)
    {
        $this->geoDistance[] = [$key, $location, $distance];
        return $this;
    }
    /**
     * SimpleQueryString to Field
     * @param $attribute
     * @param $keyword
     * @return $this
     */
    public function simpleQueryString($attribute, $keyword)
    {
        $this->simpleQueryString [] =[$attribute, $keyword];

        return $this;
    }

    /**
     * QueryString to Field
     * @param array $attributes
     * @param string $defaultOperator
     * @param string $keyword
     * @return $this
     */
    public function queryString(array $attributes, string $defaultOperator = null, string $keyword = null)
    {
        $this->queryString [] =[$attributes, $defaultOperator, $keyword];

        return $this;
    }

    /**
     * @param string $field
     * @param string $condition
     * @param array $values
     */
    public function boolShould(string $field, string $condition, array $values)
    {
        $this->boolShould [] = [$field, $condition, $values];
    }

    /**
     * Reset repository to it's default
     * @return $this
     */
    public function resetBuilder()
    {
        $this->where = [];
        $this->whereNot = [];
        $this->whereNotIn = [];
        $this->inRange = [];
        $this->notInRange = [];
        $this->exist = [];
        $this->notExists = [];
        $this->whereTerms = [];
        $this->whereOr = [];
        $this->match = [];
        $this->mismatch = [];
        $this->geoDistance = [];
        $this->simpleQueryString = [];
        $this->queryString = [];
        $this->boolShould = [];
        $this->query = new BoolQuery();
        $this->filter = new  BoolQuery();

        return $this;
    }

    /**
     * prepare query before the execution
     * @return BoolQuery
     */
    public function prepareQuery()
    {
        //prepare where conditions
        foreach ($this->where as $where) {
            $this->prepareWhereCondition($where);
        }

        //prepare where not conditions
        foreach ($this->whereNot as $whereNot) {
            $this->prepareWhereNotCondition($whereNot);
        }

        //prepare where not in conditions
        foreach ($this->whereNotIn as $whereNotIn) {
            $this->prepareWhereNotInCondition($whereNotIn);
        }

        // Add a basic range clause to the query
        foreach ($this->inRange as $inRange) {
            $this->prepareInRangeCondition($inRange);
        }

        // Add a basic not in range clause to the query
        foreach ($this->notInRange as $notInRange) {
            $this->prepareNotInRangeCondition($notInRange);
        }

        // add Terms to main query
        foreach ($this->whereTerms as $term) {
            $this->prepareWhereTermsCondition($term);
        }

        // add Terms Or to main query
        $boolOr = new BoolQuery();
        foreach ($this->whereOr as $attribute => $value) {
            $this->prepareWhereOrCondition($attribute, $value, $boolOr);
        }
        $this->filter->addMust($boolOr);

        // add exists constrains to the query
        foreach ($this->exist as $exist) {
            $this->prepareExistCondition($exist);
        }

        // add exists constrains to the query
        foreach ($this->notExists as $notExists) {
            $this->prepareNotExistsCondition($notExists);
        }

        // add matcher queries
        foreach ($this->match as $match) {
            $this->prepareMatchQueries($match);
        }

        // add mismatcher queries
        foreach ($this->mismatch as $mismatch) {
            $this->prepareMismatchQueries($mismatch);
        }

        // add geoDistance queries
        foreach ($this->geoDistance as $geoDistance) {
            $this->prepareGeoDistanceQueries($geoDistance);
        }

        // add SimpleQueryString
        foreach ($this->simpleQueryString as $query) {
            $this->prepareSimpleQueryString($query);
        }

        // add QueryString
        foreach ($this->queryString as $query) {
            $this->prepareQueryString($query);
        }

        // add BoolOr
        foreach ($this->boolShould as $query) {
            $this->prepareBoolShould($query);
        }

        $this->query->addFilter($this->filter);

        return $this->query;
    }


    /**
     * Add exist conditions to the main query
     * @param $attribute
     */
    private function prepareExistCondition($attribute)
    {
        $this->filter->addMust(new Exists($attribute));
    }

    /**
     * Add not exists conditions to the main query
     * @param $attribute
     */
    private function prepareNotExistsCondition($attribute)
    {
        $this->filter->addMustNot(new Exists($attribute));
    }

    /**
     * @param $attribute
     * @param $value
     * @param $boolOr
     */
    private function prepareWhereOrCondition($attribute, $value, &$boolOr)
    {
        $terms = new Terms();
        $terms->setTerms($attribute, $value);
        $boolOr->addShould($terms);
    }

    /**
     * Add some bool terms to the main query
     * @param array $term
     */
    private function prepareWhereTermsCondition($term)
    {
        $boolOr = new BoolQuery();
        $terms = new Terms();
        list($attribute, $value) = array_pad($term, 2, null);

        $terms->setTerms($attribute, $value);
        $boolOr->addShould($terms);

        $this->filter->addMust($boolOr);
    }

    /**
     * add where condition to main filter
     * @param array $where
     */
    private function prepareWhereCondition($where)
    {
        list($attribute, $value, $boost) = array_pad($where, 3, null);
        $subFilter = new Term();
        $subFilter->setTerm($attribute, $value, $boost);
        $this->filter->addMust($subFilter);
    }

    /**
     * add where not condition to main filter
     * @param $whereNot
     */
    private function prepareWhereNotCondition($whereNot)
    {
        list($attribute, $value, $boost) = array_pad($whereNot, 3, null);
        $subFilter = new Term();
        $subFilter->setTerm($attribute, $value, $boost);
        $this->filter->addMustNot($subFilter);
    }

    /**
     * add where not in condition to main filter
     * @param $whereNotIn
     */
    private function prepareWhereNotInCondition($whereNotIn)
    {
        list($attribute, $value) = array_pad($whereNotIn, 2, null);
        $subFilter = new Terms();
        $subFilter->setTerms($attribute, $value);
        $this->filter->addMustNot($subFilter);
    }

    /**
     * add range in to main filter
     * @param $inRange
     */
    private function prepareInRangeCondition($inRange)
    {
        list($attribute, $from, $to) = array_pad($inRange, 3, null);
        $inRange = new Range();
        $inRange->addField($attribute, ['from' => $from, 'to' => $to]);
        $this->filter->addMust($inRange);
    }

    /**
     * add Not In Range condition to the main filter
     * @param $notInRange
     */
    private function prepareNotInRangeCondition($notInRange)
    {
        list($attribute, $from, $to) = array_pad($notInRange, 3, null);
        $inRange = new Range();
        $inRange->addField($attribute, ['from' => $from, 'to' => $to]);
        $this->filter->addMustNot($inRange);
    }

    /**
     * prepare match query
     * @param $match
     */
    private function prepareMatchQueries($match)
    {
        list($attribute, $keyword) = array_pad($match, 2, null);

        $matcher = new Match();
        $matcher->setField($attribute, $keyword);
        $this->filter->addFilter($matcher);
    }

    /**
     * prepare mismatch query
     * @param $mismatch
     */
    private function prepareMismatchQueries($mismatch)
    {
        list($attribute, $keyword) = array_pad($mismatch, 2, null);

        $mismatcher = new Match();
        $mismatcher->setField($attribute, $keyword);
        $this->filter->addMustNot($mismatcher);
    }

    /**
     * prepare geoDistance query
     * @param $geoDistance
     */
    private function prepareGeoDistanceQueries($geoDistance)
    {
        list($key, $location, $distance) = array_pad($geoDistance, 3, null);
        $geoDistance = new GeoDistance($key, $location, $distance);
        $this->filter->addFilter($geoDistance);
    }
    
    /**
     * prepare Simple Query String
     * @param $query
     */
    private function prepareSimpleQueryString($query)
    {
        list($attribute, $keyword) = array_pad($query, 2, null);
        $queryString = new SimpleQueryString($keyword, $attribute);
        $this->filter->addFilter($queryString);
    }
    /**
     * prepare Simple Query String
     * @param $query
     */
    private function prepareQueryString($query)
    {
        list($attributes, $defaultOperator, $keyword) = array_pad($query, 3, null);
        $queryString = new QueryString($keyword);
        $queryString
            ->setFields($attributes)
            ->setDefaultOperator($defaultOperator)
        ;
        $this->filter->addFilter($queryString);
    }

    /**
     * @param array $query
     */
    private function prepareBoolShould(array $query)
    {
        $boolOr = new BoolQuery();

        list($field, $condition, $argument) = array_pad($query, 3, null);

        $keyUp = ucfirst($condition);
        if (method_exists($this->queryFactory, 'set'.$keyUp)) {
            $condition = call_user_func(array($this->queryFactory, 'set'.$keyUp), $field, $argument);
            $boolOr->addShould($condition);
        }

        $this->filter->addMust($boolOr);
    }
}
