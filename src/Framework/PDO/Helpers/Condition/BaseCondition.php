<?php
namespace Framework\PDO\Helpers\Condition;

abstract class BaseCondition
{
    abstract public function getSQL();

    public function __toString()
    {
        return $this->getSQL();
    }

    abstract public function getCombinedBindNames();

    abstract public function getParams();

    protected function prepareFieldName($field)
    {
        if (strpos($field, '.') !== false) {
            $segments = explode('.', $field, 2);
            return sprintf('`%s`.`%s`', $segments[0], $segments[1]);
        } else {
            return "`{$field}`";
        }
    }

    abstract public function incrementSuffix();
}
