<?php
namespace Framework\PDO\Helpers\Condition;

abstract class BaseOperatorCondition extends BaseCondition
{
    protected $conditions = [];

    /**
     * @param $conditions BaseCondition[]
     */
    public function __construct(array $conditions = [])
    {
        $conditions = array_filter($conditions);

        foreach ($conditions as $condition) {
            $this->add($condition);
        }
    }

    public function add(BaseCondition $condition)
    {
        if (!$condition instanceof BaseCondition) {
            throw new \InvalidArgumentException;
        }

        $this->conditions[] = $condition;

        $this->checkCombinedBindNamesDuplicates();
    }

    /**
     * @return BaseCondition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @return string[]
     * @throw DuplicateBindNameException
     */
    public function getCombinedBindNames()
    {
        $bindNames = [];

        foreach ($this->conditions as $condition) {
            /* @var $condition BaseCondition */
            $count = 0;
            while (count($intersectedNames = array_intersect($bindNames, $condition->getCombinedBindNames())) > 0) {
                $condition->incrementSuffix();
                if ($count++ > 100) {
                    throw new DuplicateBindNameException('Duplicate bind name(s): ' . join(', ', $intersectedNames));
                }
            }
            $bindNames = array_merge($bindNames, $condition->getCombinedBindNames());
        }

        return $bindNames;
    }

    /**
     * @throws DuplicateBindNameException
     */
    public function checkCombinedBindNamesDuplicates()
    {
        $this->getCombinedBindNames();
    }

    public function getParams()
    {
        return array_reduce(
            $this->conditions,
            function ($carry, BaseCondition $condition) {
                return array_merge($carry, $condition->getParams());
            },
            []
        );
    }

    public function incrementSuffix()
    {
        foreach ($this->conditions as $condition) {
            /* @var $condition BaseCondition */
            $condition->incrementSuffix();
        }
    }
}
