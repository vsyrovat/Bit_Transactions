<?php

declare(strict_types=1);

namespace Framework\Pagination;

class Paginator
{
    private $itemsBefore;
    private $itemsAfter;
    private $pageId;
    private $totalPages;
    private $firstNum = 1;
    private $lastNum;
    private $pager;
    private $enableToStart;
    private $linkToStart;
    private $enableToPrev;
    private $linkToPrev;
    private $enableToEnd;
    private $linkToEnd;
    private $enableToNext;
    private $linkToNext;
    private $enableFirst = false;
    private $enableLast = false;
    private $enablePredots = false;
    private $enablePostdots = false;
    private $pre = [];
    private $post = [];
    private $itemsCount;
    private $itemsCountTotal;
    private $startNum;
    private $endNum;

    public function __construct(Pager $pager, int $itemsCount, int $itemsCountTotal, int $itemsBefore = 3, int $itemsAfter = 3)
    {
        $this->pager = $pager;
        $this->pageId = $pager->getPageNum();
        $this->totalPages = intval(ceil($itemsCountTotal / $pager->getPageSize()));
        $this->itemsCount = $itemsCount;
        $this->itemsCountTotal = $itemsCountTotal;

        $this->itemsBefore = $itemsBefore;
        $this->itemsAfter = $itemsAfter;

        $this->lastNum = $this->totalPages + $this->firstNum - 1;

        $this->enableToStart = $this->pageId > $this->firstNum;
        $this->linkToStart = $this->getLink($this->firstNum);

        $this->enableToPrev = $this->pageId > $this->firstNum;
        $this->linkToPrev = $this->getLink($this->pageId - 1);

        $this->enableToEnd = $this->pageId < $this->lastNum;
        $this->linkToEnd = $this->getLink($this->lastNum);

        $this->enableToNext = $this->pageId < $this->lastNum;
        $this->linkToNext = $this->getLink($this->pageId + 1);

        if ($this->pageId > $this->lastNum) {
            $this->pageId = $this->totalPages;
        }
        if ($this->pageId < $this->firstNum) {
            $this->pageId = $this->firstNum;
        }

        if ($this->pageId <= $this->itemsBefore) {
            $this->itemsBefore = $this->pageId - $this->firstNum;
        } elseif ($this->itemsBefore + 2 == ($this->pageId - $this->firstNum + 1)) {
            $this->itemsBefore++;
        } elseif ($this->itemsBefore + 2 < ($this->pageId - $this->firstNum + 1)) {
            $this->enableFirst = true;
            $this->enablePredots = true;
        }

        if ($this->totalPages - $this->pageId < $this->itemsAfter) {
            $this->itemsAfter = $this->lastNum - $this->pageId;
        } elseif ($this->itemsAfter + 2 == ($this->lastNum - $this->pageId + 1)) {
            $this->itemsAfter++;
        } elseif ($this->itemsAfter + 2 < ($this->lastNum - $this->pageId + 1)) {
            $this->enableLast = true;
            $this->enablePostdots = true;
        }

        $this->pre = [];
        if ($this->pageId > $this->firstNum) {
            for ($i = $this->pageId - $this->itemsBefore; $i <= $this->pageId - 1; $i++) {
                $this->pre[$i] = $this->getLink($i);
            }
        }

        $this->post = array();
        if ($this->pageId < $this->lastNum) {
            for ($i = $this->pageId + 1; $i <= $this->pageId + $this->itemsAfter; $i++) {
                $this->post[$i] = $this->getLink($i);
            }
        }

        $this->startNum = $this->firstNum;
        $this->endNum = $this->lastNum;
    }

    private function getLink($pageId): string
    {
        return $this->pager->getLink($pageId);
    }

    public function enableToStart(): bool
    {
        return $this->enableToStart;
    }

    public function enableToPrev(): bool
    {
        return $this->enableToPrev;
    }

    public function enableFirst(): bool
    {
        return $this->enableFirst;
    }

    public function enablePreDots(): bool
    {
        return $this->enablePredots;
    }

    public function pre(): array
    {
        return $this->pre;
    }

    public function pageId(): int
    {
        return $this->pageId;
    }

    public function post(): array
    {
        return $this->post;
    }

    public function enablePostDots(): bool
    {
        return $this->enablePostdots;
    }

    public function enableLast(): bool
    {
        return $this->enableLast;
    }

    public function enableToNext(): bool
    {
        return $this->enableToNext;
    }

    public function linkToNext(): string
    {
        return $this->linkToNext;
    }

    public function enableToEnd(): bool
    {
        return $this->enableToEnd;
    }

    public function linkToEnd(): string
    {
        return $this->linkToEnd;
    }

    public function linkToStart(): string
    {
        return $this->linkToStart;
    }

    public function linkToPrev(): string
    {
        return $this->linkToPrev;
    }

    public function countFrom(): int
    {
        return $this->countTo() > 0 ? $this->pager->getOffset() + 1 : 0;
    }

    public function countTo(): int
    {
        return $this->pager->getOffset() + $this->itemsCount;
    }

    public function countTotal(): int
    {
        return $this->itemsCountTotal;
    }

    public function startNum(): int
    {
        return $this->startNum;
    }

    public function endNum(): int
    {
        return $this->endNum;
    }
}
