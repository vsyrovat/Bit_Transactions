<?php
namespace Framework\PDO\Helpers\Condition;

abstract class BaseCompareCondition extends BaseCondition
{
    protected $field;
    protected $value;
    protected $paramType;
    protected $bindName;
    protected $suffix;

    /**
     * BaseCompareCondition constructor.
     * @param string $field
     * @param string|integer|float|boolean|null $value
     * @param int $paramType
     * @param string|null $bindName
     */
    public function __construct($field, $value, $paramType = \PDO::PARAM_STR, $bindName = null)
    {
        $this->field = $this->prepareFieldName($field);
        $this->value = $value;
        $this->paramType = $paramType ?: null;
        $this->bindName = $this->prepareBindName($bindName ?: $field);
    }

    public function getField()
    {
        return $this->field;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getParamType()
    {
        return $this->paramType;
    }

    public function getBindName()
    {
        return $this->bindName . $this->suffix;
    }

    public function getCombinedBindNames()
    {
        return [$this->getBindName()];
    }

    /**
     * @return int|null
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    public function incrementSuffix()
    {
        $this->suffix++;
    }

    public function getParams()
    {
        return [$this->getBindName() => $this->value];
    }

    protected function prepareBindName($bindName)
    {
        return str_replace('.', '_', $bindName);
    }
}
