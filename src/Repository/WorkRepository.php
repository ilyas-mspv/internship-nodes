<?php

namespace App\Repository;

use App\Entity\Work;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Work|null find($id, $lockMode = null, $lockVersion = null)
 * @method Work|null findOneBy(array $criteria, array $orderBy = null)
 * @method Work[]    findAll()
 * @method Work[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Work::class);
    }

    /**
     * @param $employee_id
     * @return float
     * @throws NonUniqueResultException
     */
    public function findRateSum($employee_id)
    {
        return (float) $this->createQueryBuilder('w')
            ->select("sum(w.rate) as sum")
            ->andWhere('w.employee_id = :val')
            ->setParameter('val', $employee_id)
            ->orderBy('w.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()["sum"]
        ;
    }

    /**
     * @param $value
     * @return Work[]
     */
    public function findWorksByEmployeeId($value): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.employee_id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @param $value
     * @return Work[]
     */
    public function findEmployeesByNodeId($value): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.node_id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $nid
     * @param $eid
     * @return Work
     * @throws NonUniqueResultException
     */
    public function findWorkByNodeAndEmployeeIds($nid, $eid){
        return $this->createQueryBuilder('w')
            ->andWhere('w.employee_id = :eid AND w.node_id = :nid')
            ->setParameter('eid', $eid)
            ->setParameter('nid', $nid)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

}
