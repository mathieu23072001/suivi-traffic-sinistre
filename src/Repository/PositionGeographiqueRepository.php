<?php

namespace App\Repository;

use App\Entity\PositionGeographique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PositionGeographique>
 *
 * @method PositionGeographique|null find($id, $lockMode = null, $lockVersion = null)
 * @method PositionGeographique|null findOneBy(array $criteria, array $orderBy = null)
 * @method PositionGeographique[]    findAll()
 * @method PositionGeographique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PositionGeographiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PositionGeographique::class);
    }

    public function save(PositionGeographique $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PositionGeographique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PositionGeographique[] Returns an array of PositionGeographique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PositionGeographique
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getAllPoints()
    {
                return $this->createQueryBuilder('p')
            ->andWhere('p.actif = :val')
            ->setParameter('val', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function desactiverPoint(mixed $echantillons)
    {
        foreach ($echantillons as $echantillon) {
            $echantillon->setActif(false);
            $this->save($echantillon);
        }
    }
}
