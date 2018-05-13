<?php
namespace Framework\PDO\Helpers\Condition;

class Equal extends BaseCompareCondition
{
    public function getSQL()
    {
        return "({$this->field}=:{$this->getBindName()})";
    }
}
