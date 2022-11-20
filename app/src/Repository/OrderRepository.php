<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Order;
use App\Utils\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    final public function getList(int $page, User $user): Paginator
    {
        $qb = $this->createQueryBuilder('o')
        ->andWhere('o.owner = '.$user->getId())
        ->orderBy('o.id', 'ASC');

        return (new Paginator($qb))->pagination($page);
    }

    public function isUserHaveBook(User $user, Book $book): ?Order
    {
        return $this->createQueryBuilder('o')
        ->innerJoin('o.book', 'b')
        ->andWhere('o.status = :status')
        ->andWhere('o.owner = :userId')
        ->andWhere('b.id = :bookId')
        ->setParameter('userId', $user->getId())
        ->setParameter('bookId', $book->getId())
        ->setParameter('status', Order::STATUS_RECEIVED)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
