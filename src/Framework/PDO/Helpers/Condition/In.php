<?php
namespace Framework\PDO\Helpers\Condition;

class In extends BaseCompareCondition
{
    public function __construct($field, array $values, $paramType = \PDO::PARAM_STR, $bindName = null)
    {
        $this->field = $this->prepareFieldName($field);
        $this->value = $values;
        $this->paramType = $paramType ?: null;
        $this->bindName = $this->prepareBindName($bindName ?: $field);
    }

    public function getSQL()
    {
        $placeholders = [];
        foreach ($this->getCombinedBindNames() as $bindName) {
            $placeholders[] = ':'.$bindName;
        }

        return "({$this->field} IN (".join(',', $placeholders)."))";
    }

    public function getCombinedBindNames()
    {
        return array_keys($this->getParams());
    }

    public function getParams()
    {
        $params = [];

        $counter = 1;

        foreach ($this->value as $value) {
            $params[$this->getBindName() . $counter] = $value;
            $counter++;
        }

        return $params;
    }
}
