<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function listAuthorByEmail()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.email', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findAuthorsByBookCountRange($minBooks, $maxBooks)
    {
        $query = $this->createQueryBuilder('a');

        if ($minBooks !== null) {
            $query->andWhere('a.nb_books >= :minBooks')
                ->setParameter('minBooks', $minBooks);
        }

        if ($maxBooks !== null) {
            $query->andWhere('a.nb_books <= :maxBooks')
                ->setParameter('maxBooks', $maxBooks);
        }

        return $query->getQuery()->getResult();
    }
    public function deleteAuthorsWithZeroBooks()
{
    return $this->createQueryBuilder('a')
        ->delete()
        ->where('a.nb_books = 0')
        ->getQuery()
        ->execute();
}
}
//    /**
//     * @return Author[] Returns an array of Author objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

