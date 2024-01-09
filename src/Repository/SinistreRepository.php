<?php

namespace App\Repository;

use App\Entity\Sinistre;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sinistre>
 *
 * @method Sinistre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sinistre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sinistre[]    findAll()
 * @method Sinistre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SinistreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sinistre::class);
    }

    public function save(Sinistre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sinistre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Sinistre[] Returns an array of Sinistre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sinistre
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
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

    public function findAllToPublish()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.published = :valeur')
            ->setParameter('valeur', true)
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function countSinistrePublies()
    {
        return $this->count([
            "published" => true
        ]);
    }
}
