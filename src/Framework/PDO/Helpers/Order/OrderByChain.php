<?php

namespace Framework\PDO\Helpers\Order;

class OrderByChain extends BaseOrderBy
{
    private $orderItems = [];

    public function __construct(OrderBy ...$items)
    {
        foreach ($items as $item)
        {
            $this->add($item);
        }
    }

    public function add(OrderBy $orderBy): self
    {
        $this->orderItems[] = $orderBy;

        return $this;
    }

    public function getSQL(): ?string
    {
        return join(', ', array_map(function(OrderBy $item){ return $item->getSQL(); }, $this->orderItems));
    }
}
