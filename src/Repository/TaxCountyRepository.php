<?php

namespace App\Repository;

use App\Entity\TaxCounty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TaxCounty|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxCounty|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxCounty[]    findAll()
 * @method TaxCounty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxCountyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxCounty::class);
    }
}
