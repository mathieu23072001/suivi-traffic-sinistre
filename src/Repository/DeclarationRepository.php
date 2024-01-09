<?php

namespace App\Repository;

use App\Entity\Declaration;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Declaration>
 *
 * @method Declaration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Declaration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Declaration[]    findAll()
 * @method Declaration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeclarationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Declaration::class);
    }

    public function save(Declaration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
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
    public function remove(Declaration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Declaration[] Returns an array of Declaration objects
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

//    public function findOneBySomeField($value): ?Declaration
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getAllForAbonne(Utilisateur $utilisateur)
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
