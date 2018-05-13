<?php
namespace Framework\PDO\Helpers\Condition;

class Greater extends BaseCompareCondition
{
    public function getSQL()
    {
        return "({$this->field}>:{$this->getBindName()})";
    }
}
