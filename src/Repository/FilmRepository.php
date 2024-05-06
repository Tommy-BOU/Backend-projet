<?php

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Film>
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    public function findFilmsAndCategs(): array
    {
        return $this->createQueryBuilder('f')
            ->select('f', 'fc', 'c') // Select all relevant entities
            ->join('f.filmCategories', 'fc') // Left join FilmCategory entity
            ->join('fc.category', 'c') // Left join Category entity
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFilmByCateg(int $id): array
    {
        return $this->createQueryBuilder('f')
            ->select('f', 'fc', 'c')
            ->join('f.filmCategories', 'fc')
            ->join('fc.category', 'c')
            ->andWhere('fc.category = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult();
    }

    public function findFilmAndCategs(int $id): array
    {
        return $this->createQueryBuilder('f')
            ->select('f', 'fc', 'c')
            ->join('f.filmCategories', 'fc')
            ->join('fc.category', 'c')
            ->andWhere('f.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Film[] Returns an array of Film objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Film
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
