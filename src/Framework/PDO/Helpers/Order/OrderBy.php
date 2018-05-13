<?php

declare(strict_types=1);

namespace Framework\PDO\Helpers\Order;

class OrderBy extends BaseOrderBy
{
    private $field;
    private $ascDesc;

    public function __construct(?string $field, ?string $ascDesc = null)
    {
        $this->field = $field;

        if ($field !== null) {
            switch (strtoupper($ascDesc)) {
                case 'ASC':
                    $this->ascDesc = 'ASC';
                    break;
                case 'DESC':
                    $this->ascDesc = 'DESC';
                    break;
                default:
                    throw new \InvalidArgumentException('Unknown sort order: '.$ascDesc.', expected ASC or DESC');
            }
        }
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getAscDesc(): string
    {
        return $this->ascDesc;
    }

    public function getSQL(): ?string
    {
        if ($this->field === null) {
            return null;
        }

        return $this->prepareFieldName($this->field).' '.$this->ascDesc;
    }
}
