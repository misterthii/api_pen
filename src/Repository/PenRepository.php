<?php

namespace App\Repository;

use App\Entity\Pen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pen>
 *
 * @method Pen|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pen|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pen[]    findAll()
 * @method Pen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pen::class);
    }

    public function selectAllPenWithBrandAndTypeAndColorAndMaterial()
    {
        $queryBuilder = $this->createQueryBuilder('pen');
        $queryBuilder->select('pen', 'brand', 'type', 'color', 'material')
            ->join('pen.brand', 'brand')
            ->join('pen.type', 'type')
            ->join('pen.colors', 'color')
            ->join('pen.material', 'material');

        return $queryBuilder->getQuery()->getResult();
    }

//    /**
//     * @return Pen[] Returns an array of Pen objects
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

//    public function findOneBySomeField($value): ?Pen
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
