<?php

namespace Tests\FilterParsers;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use PHPUnit\Framework\TestCase;

use Pz\LaravelDoctrine\JsonApi\FilterParsers\SearchFilterParser;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;

class FilterStringParserTest extends TestCase
{
    public function testArrayKeySearch()
    {
        $request = new JsonApiRequest(['filter' => ['search' => 'queryString']]);
        $parser = new SearchFilterParser($request, 'testField');

        /** @var Criteria $criteria */
        $criteria = $parser(Criteria::create());

        /** @var Comparison $where */
        $where = $criteria->getWhereExpression();

        $this->assertEquals('testField', $where->getField());
        $this->assertEquals(Comparison::CONTAINS, $where->getOperator());
        $this->assertEquals('queryString', $where->getValue()->getValue());
    }

    public function testPropertyQueryParser()
    {
        $request = new JsonApiRequest(['filter' => ['search' => 'queryString']]);
        $parser = new SearchFilterParser($request, 'testField');

        /** @var Criteria $criteria */
        $criteria = $parser(Criteria::create());

        /** @var Comparison $where */
        $where = $criteria->getWhereExpression();

        $this->assertEquals('testField', $where->getField());
        $this->assertEquals(Comparison::CONTAINS, $where->getOperator());
        $this->assertEquals('queryString', $where->getValue()->getValue());
    }
}
