<?php

namespace App\Repository;

use App\Entity\InformationUtile;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InformationUtile>
 *
 * @method InformationUtile|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformationUtile|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformationUtile[]    findAll()
 * @method InformationUtile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformationUtileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationUtile::class);
    }

    public function save(InformationUtile $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(InformationUtile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return InformationUtile[] Returns an array of InformationUtile objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InformationUtile
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function countInfo()
    {
        return $this->count([
            "published" => true
        ]);
    }

    public function findAllForAbonne(Utilisateur $utilisateur)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.utilisateur = :valeur')
            ->setParameter('valeur', $utilisateur)
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
}
