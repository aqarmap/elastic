<?php

/**
 * Created by PhpStorm.
 * User: hafez
 * Date: 28/11/18
 * Time: 21:39
 */
namespace ElasticRepository\Factory;

use Elastica\Query\Range;
use Elastica\Query\Terms;

class QueryFactory
{
    /**
     * @param string $field
     * @param array $arg
     * @return Range
     */
    public function setRange(string $field, array $arg): Range
    {
        return new Range($field, $arg);
    }

    /**
     * @param string $field
     * @param array $arg
     * @return Terms
     */
    public function setTerms(string $field, array $arg): Terms
    {
        return new Terms($field, $arg);
    }
}
