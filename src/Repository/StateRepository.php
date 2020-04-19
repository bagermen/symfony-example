<?php

namespace App\Repository;

use App\Entity\State;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method State|null find($id, $lockMode = null, $lockVersion = null)
 * @method State|null findOneBy(array $criteria, array $orderBy = null)
 * @method State[]    findAll()
 * @method State[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, State::class);
    }

    /**
     * Find states by codes
     * @params string[] list of codes
     * @return mixed
     */
    public function findByCountries(array $codes)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        if (!$codes) {
            return [];
        }

        if (!is_array($codes)) {
            $codes = [$codes];
        }

        $qb->select('s')
            ->from('App\Entity\State', 's')
            ->where($qb->expr()->in('s.country', ':codes'))
            ->setParameter('codes', $codes);

        $query = $qb->getQuery();

        return $query->execute();
    }
}
