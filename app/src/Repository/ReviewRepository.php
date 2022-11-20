<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Review;
use App\Entity\User;
use App\Utils\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function save(Review $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Review $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    final public function getList(int $bookId, int $page): Paginator
    {
        $qb = $this->createQueryBuilder('review')
            ->andWhere('review.book = :book')
            ->setParameter('book', $bookId)
            ->orderBy('review.id', 'ASC');

        return (new Paginator($qb))->pagination($page);
    }

    final public function getAverage(int $bookId): float
    {
        $qb = $this->createQueryBuilder('r')
        ->select('SUM(r.rating)/COUNT(r.rating)')
        ->andWhere('r.rating IS NOT NULL')
        ->andWhere('r.book = :id')
        ->setParameter('id', $bookId)
        ->getQuery()
        ->getSingleScalarResult();

        return (float) $qb;
    }
}
