<?php

namespace App\Repository;

use App\Entity\TaxIncome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TaxIncome|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxIncome|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxIncome[]    findAll()
 * @method TaxIncome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxIncomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxIncome::class);
    }

    public function removeAll()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->delete(TaxIncome::class);

        $query = $qb->getQuery();

        $query->execute();
    }
}
