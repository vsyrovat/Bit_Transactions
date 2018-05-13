<?php
namespace Framework\PDO\Helpers\Condition;

class OrCondition extends BaseOperatorCondition
{
    public function getSQL()
    {
        $conditionsSQL = array_filter(
            array_map(
                function (BaseCondition $c) {
                    return $c->getSQL();
                },
                $this->conditions
            )
        );

        if (count($conditionsSQL) > 1) {
            return '(' . join(' OR ', $this->conditions) . ')';
        } elseif (count($conditionsSQL) == 1) {
            $condition = reset($this->conditions);
            return $condition->getSQL();
        }
        return "";
    }
}
