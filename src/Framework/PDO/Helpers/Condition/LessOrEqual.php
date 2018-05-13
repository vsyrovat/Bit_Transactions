<?php
namespace Framework\PDO\Helpers\Condition;

class LessOrEqual extends BaseCompareCondition
{
    public function getSQL()
    {
        return "({$this->field}<=:{$this->getBindName()})";
    }
}
