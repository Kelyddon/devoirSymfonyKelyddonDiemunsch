<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
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

    /**
     * Retourne la liste des genres distincts présents dans la table book
     */
    public function findDistinctGenres(): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select('DISTINCT b.genre')
            ->where('b.genre IS NOT NULL')
            ->orderBy('b.genre', 'ASC');
        $result = $qb->getQuery()->getResult();
        return array_map(fn($row) => $row['genre'], $result);
    }

    /**
     * Recherche les livres par genre et par titre
     */
    public function findByGenreAndTitle(?string $genre, ?string $search): array
    {
        $qb = $this->createQueryBuilder('b');
        if ($genre) {
            $qb->andWhere('b.genre = :genre')
                ->setParameter('genre', $genre);
        }
        if ($search) {
            $qb->andWhere('b.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        $qb->orderBy('b.createdAt', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
