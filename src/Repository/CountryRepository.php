<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * Find countries by codes
     * @params string[] list of codes
     * @return mixed
     */
    public function findByCodes(array $codes)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        if (!$codes) {
            return [];
        }

        if (!is_array($codes)) {
            $codes = [$codes];
        }

        $qb->select('c')
            ->from(Country::class, 'c')
            ->where($qb->expr()->in('c.code', ':codes'))
            ->setParameter('codes', $codes);

        $query = $qb->getQuery();

        return $query->execute();
    }
}