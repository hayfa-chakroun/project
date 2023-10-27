<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
    public function searchBookByRef($ref)
    {
        return $this->createQueryBuilder('b')
            ->where('b.ref = :ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getResult();
    }
    public function booksListByAuthors()
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->orderBy('a.username', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findBooksPublishedBefore2023WithAuthorMoreThan10Books()
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->where('b.publicationDate < :date')
            ->andWhere('a.nb_books > 10')
            ->setParameter('date', new \DateTime('2023-10-01'))
            ->getQuery()
            ->getResult();
            
    }
    public function updateCategoryToRomance()
    {
        $queryBuilder = $this->createQueryBuilder('b');
        $queryBuilder
            ->update()
            ->set('b.category', ':newCategory')
            ->where('b.category = :oldCategory')
            ->setParameter('newCategory', 'Romance')
            ->setParameter('oldCategory', 'Science-Fiction');

        return $queryBuilder->getQuery()->execute();
    }
    public function countBooksInCategory($category)
    {
        return $this->createQueryBuilder('b')
            ->select('COUNT(b)')
            ->where('b.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findBooksPublishedBetweenDates($startDate, $endDate)
    {
        return $this->createQueryBuilder('b')
            ->where('b.publicationDate >= :startDate')
            ->andWhere('b.publicationDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
