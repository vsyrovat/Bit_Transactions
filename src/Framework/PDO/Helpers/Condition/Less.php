<?php
namespace Framework\PDO\Helpers\Condition;

class Less extends BaseCompareCondition
{
    public function getSQL()
    {
        return "({$this->field}<:{$this->getBindName()})";
    }
}
