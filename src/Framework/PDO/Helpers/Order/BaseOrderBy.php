<?php

declare(strict_types=1);

namespace Framework\PDO\Helpers\Order;

abstract class BaseOrderBy
{
    abstract public function getSQL(): ?string;

    public function __toString()
    {
        return $this->getSQL();
    }

    protected function prepareFieldName($field)
    {
        if (strpos($field, '.') !== false) {
            $segments = explode('.', $field, 2);
            return sprintf('`%s`.`%s`', $segments[0], $segments[1]);
        } else {
            return "`{$field}`";
        }
    }
}
