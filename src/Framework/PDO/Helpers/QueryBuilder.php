<?php

declare(strict_types=1);

namespace Framework\PDO\Helpers;

use Framework\PDO\Helpers\Condition\BaseCondition;
use Framework\PDO\Helpers\Condition\DuplicateBindNameException;
use Framework\PDO\Helpers\Order\BaseOrderBy;

class QueryBuilder
{
    private $baseQueryString;
    private $params = [];
    private $paramTypes = [];
    private $replaceRules = [];
    private $whereSearchSubstring;
    private $whereCondition;
    private $orderSearchSubstring;
    private $orderCondition;

    /**
     * SQLReplacer constructor.
     * @param string $queryString
     * @param array $params
     * @param array $paramTypes
     */
    public function __construct($queryString, array $params = [], array $paramTypes = [])
    {
        $this->baseQueryString = $queryString;
        $this->params = $params;
        $this->paramTypes = $paramTypes;
    }

    public function prepareLimit($searchSubstring, $limit, $offset)
    {
        if (!is_null($limit) && !is_null($offset)) {
            $limit = intval($limit);
            $offset = intval($offset);
            $replaceSubstring = 'LIMIT :offset,:limit';
            $this->params['limit'] = $limit;
            $this->paramTypes['limit'] = \PDO::PARAM_INT;
            $this->params['offset'] = $offset;
            $this->paramTypes['offset'] = \PDO::PARAM_INT;
        } else {
            $replaceSubstring = ''; // No limit condition
        }

        $this->replaceRules[$searchSubstring] = $replaceSubstring;

        return $this;
    }

    public function prepareWhere($searchSubstring, BaseCondition $condition)
    {
        $this->whereSearchSubstring = $searchSubstring;
        $this->whereCondition = $condition;
        return $this;
    }

    public function prepareOrderBy($searchSubstring, BaseOrderBy $orderBy): self
    {
        $this->orderSearchSubstring = $searchSubstring;
        $this->orderCondition = $orderBy;
        return $this;
    }

    public function getQuery()
    {
        $this->getCombinedBindNames();

        if ($this->whereCondition instanceof BaseCondition) {
            if ($this->whereCondition->getSQL()) {
                $this->replaceRules[$this->whereSearchSubstring] = 'WHERE ' . $this->whereCondition->getSQL();
            } else {
                $this->replaceRules[$this->whereSearchSubstring] = '';
            }
        }

        if ($this->orderCondition instanceof BaseOrderBy) {
            if ($this->orderCondition->getSQL()) {
                $this->replaceRules[$this->orderSearchSubstring] = 'ORDER BY '.$this->orderCondition->getSQL();
            } else {
                $this->replaceRules[$this->orderSearchSubstring] = '';
            }
        }

        return strtr($this->baseQueryString, $this->replaceRules);
    }

    public function getParamTypes()
    {
        return $this->paramTypes;
    }

    public function getParams()
    {
        if ($this->whereCondition instanceof BaseCondition) {
            return array_merge($this->params, $this->whereCondition->getParams());
        } else {
            return $this->params;
        }
    }

    /**
     * @return BaseCondition|null
     */
    public function getWhereCondition()
    {
        return $this->whereCondition;
    }

    public function getCombinedBindNames()
    {
        $bindNames = array_keys($this->params);

        if ($this->whereCondition instanceof BaseCondition) {
            $count = 0;
            while (count($intersectedNames = array_intersect($bindNames, $this->whereCondition->getCombinedBindNames())) > 0) {
                $this->whereCondition->incrementSuffix();
                if ($count++ > 100) {
                    throw new DuplicateBindNameException('Duplicate bind name(s): ' . join(', ', $intersectedNames));
                }
            }
            $bindNames = array_merge($bindNames, $this->whereCondition->getCombinedBindNames());
        }

        return $bindNames;
    }

    public function incrementSuffix()
    {
        if ($this->whereCondition instanceof BaseCondition) {
            $this->whereCondition->incrementSuffix();
        }
    }
}
