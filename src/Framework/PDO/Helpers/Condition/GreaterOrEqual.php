<?php
namespace Framework\PDO\Helpers\Condition;

class GreaterOrEqual extends BaseCompareCondition
{
    public function getSQL()
    {
        return "({$this->field}>=:{$this->getBindName()})";
    }
}
