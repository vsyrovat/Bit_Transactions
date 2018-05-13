<?php

declare(strict_types=1);

namespace Framework\Pagination;

use Symfony\Component\HttpFoundation\Request;

class Pager
{
    use UrlParamsTrait;

    private $request;
    private $pageParamName;
    private $pageSize;
    private $pageNum;
    private $limit;
    private $offset;
    private $currentUri;

    public function __construct(Request $request, string $pageParamName, int $pageSize)
    {
        $this->request = $request;
        $this->pageParamName = $pageParamName;
        $this->pageSize = $pageSize;

        $this->pageNum = $this->request->get($this->pageParamName);
        $this->pageNum = intval($this->pageNum);

        if ($this->pageNum < 1) {
            $this->pageNum = 1;
        }

        $this->limit = $this->pageSize;
        $this->offset = ($this->pageNum - 1) * $this->limit;

        $this->currentUri = $this->request->getRequestUri();
    }

    public function getPageNum(): int
    {
        return $this->pageNum;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLink($pageId): string
    {
        return $this->addUrlParams($this->currentUri, [$this->pageParamName => $pageId]);
    }
}
