<?php

namespace App\Utils;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;

class Paginator
{
    private const PAGE_SIZE = 4;
    private ArrayIterator $result;
    private int $numResult;
    private int $currentPage;

    public function __construct(private QueryBuilder $qb, private int $pageSize = self::PAGE_SIZE)
    {
    }

    /**
     * @param int $page
     * @return $this
     */
    public function pagination(int $page = 1): self
    {
        $this->currentPage = max(1, $page);
        
        $query = $this->qb
            ->setFirstResult(($this->currentPage - 1) * $this->pageSize)
            ->setMaxResults($this->pageSize)
            ->getQuery();

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query, true);

        $this->result = $paginator->getIterator();
        $this->numResult = $paginator->count();

        return $this;
    }
    
    public function getResult(): ArrayIterator
    {
        return $this->result;
    }

    public function getNumResult(): int
    {
        return $this->numResult;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getLastPage(): int
    {
        return (int) ceil($this->numResult / $this->pageSize);
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

    public function hasToPaginate(): bool
    {
        return $this->numResult > $this->pageSize;
    }
}