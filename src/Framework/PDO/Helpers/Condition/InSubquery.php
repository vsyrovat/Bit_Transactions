<?php
namespace Framework\PDO\Helpers\Condition;

use Framework\PDO\Helpers\QueryBuilder;

class InSubquery extends BaseCompareCondition
{
    protected $field;
    protected $queryBuilder;

    public function __construct($field, QueryBuilder $queryBuilder)
    {
        $this->field = $this->prepareFieldName($field);
        $this->queryBuilder = $queryBuilder;
    }

    public function getSQL()
    {
        return "({$this->field} IN (".$this->queryBuilder->getQuery()."))";
    }

    public function getParams()
    {
        return $this->queryBuilder->getParams();
    }

    public function getCombinedBindNames()
    {
        return $this->queryBuilder->getCombinedBindNames();
    }

    public function incrementSuffix()
    {
        $this->queryBuilder->incrementSuffix();
    }
}
