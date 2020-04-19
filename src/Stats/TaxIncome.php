<?php
namespace App\Stats;

use App\Entity\Country;
use App\Entity\County;
use App\Entity\State;
use App\Entity\TaxCounty;
use App\Entity\TaxIncome as TaxIncomeEntity;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
/**
 * Statistic calculator
 */
class TaxIncome
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Output the collected overall taxes of the country
     * I'm using DQL to get results here
     * @param Country $country
     * @param \DateTime $from
     * @param \DateTime $to
     * @return int
     */
    public function getCountryTax(Country $country, \DateTime $from, \DateTime $to): int
    {
        /**
         * @var QueryBuilder
         */
        $qb = $this->em->createQueryBuilder();

        $qb->select('SUM(t.amount)')
            ->from(Country::class, 'c')
                ->leftJoin(State::class, 's', Join::WITH, 's.country = c.code')
                ->leftJoin(County::class, 'co', Join::WITH, 'co.state = s.id')
                ->leftJoin(TaxIncomeEntity::class, 'ti', Join::WITH, 'ti.county = co.id')
                ->leftJoin(TaxCounty::class, 't', Join::WITH, 't.tax = ti.tax and ti.county =t.county')
            ->groupBy('c.code')
            ->where($qb->expr()->between('ti.date', ':from', ':to'))
            ->andWhere($qb->expr()->eq('c', ':country'))
                ->setParameter(':country', $country)
                ->setParameter(':from', $from)
                ->setParameter(':to', $to);

        return (int) $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * Output the average tax rate of the country
     * I'm using DQL to get results here
     * @param Country $country
     * @param \DateTime $from
     * @param \DateTime $to
     * @return int
     */
    public function getAvgTaxRate(Country $country, \DateTime $from, \DateTime $to): int
    {
        /**
         * @var QueryBuilder
         */
        $qb = $this->em->createQueryBuilder();

        $qb->select('AVG(t.amount)')
            ->from(Country::class, 'c')
                ->leftJoin(State::class, 's', Join::WITH, 's.country = c.code')
                ->leftJoin(County::class, 'co', Join::WITH, 'co.state = s.id')
                ->leftJoin(TaxIncomeEntity::class, 'ti', Join::WITH, 'ti.county = co.id')
                ->leftJoin(TaxCounty::class, 't', Join::WITH, 't.tax = ti.tax and ti.county =t.county')
            ->groupBy('c.code')
            ->where($qb->expr()->between('ti.date', ':from', ':to'))
            ->andWhere($qb->expr()->eq('c', ':country'))
                ->setParameter(':country', $country)
                ->setParameter(':from', $from)
                ->setParameter(':to', $to);

        return (int) $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * Output the average amount of taxes collected per state
     * DBAL layer usage (native SQL)
     * We have to use it because DQL are not allowed to be used with subqueries
     * @param Country $country
     * @param \DateTime $from
     * @param \DateTime $to
     * @return int
     */
    public function getAvgAmountPerState(Country $country, \DateTime $from, \DateTime $to): int
    {
        $conn = $this->em->getConnection();

        $subquery = <<<SQL
select sum(tc.amount) total
from state s
    left join county co on co.state_id = s.id
    left join tax_income ti on ti.county_id = co.id
    left join tax_county tc on tc.tax_code = ti.tax_code and ti.county_id =tc.county_id
where (ti.date between :from and :to) and s.country_code = :country
group by s.id
SQL;

        $sql = <<<SQL
select AVG(t.total) as c
from ({$subquery}) as t
SQL;

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('country', $country->getCode());
        /**
         * Warning!! Server should use the same timezone as MySQL server
         */
        $stmt->bindValue('from', $from->format('Y-m-d H:i:s'));
        $stmt->bindValue('to', $to->format('Y-m-d H:i:s'));
        $stmt->execute();

        return (int) $stmt->fetchColumn(0);
    }

    /**
     * Output the overall amount of taxes collected per state
     * Output the average county tax rate per state
     * DBAL layer usage (native SQL)
     * @param Country $country
     * @param \DateTime $from
     * @param \DateTime $to
     * @return int
     */
    public function getStateTaxRateStats(Country $country, \DateTime $from, \DateTime $to): array
    {
        $conn = $this->em->getConnection();

        $sql = <<<SQL
select s.id, s.name, sum(tc.amount) total, avg(tc.amount) average
from state s
    left join county co on co.state_id = s.id
    left join tax_income ti on ti.county_id = co.id
    left join tax_county tc on tc.tax_code = ti.tax_code and ti.county_id =tc.county_id
where (ti.date between :from and :to) and s.country_code = :country
group by s.id
SQL;

        $stmt = $conn->prepare($sql);

        $stmt->bindValue('country', $country->getCode());
        /**
         * Warning!! Server should use the same timezone as MySQL server
         */
        $stmt->bindValue('from', $from->format('Y-m-d H:i:s'));
        $stmt->bindValue('to', $to->format('Y-m-d H:i:s'));

        $stmt->execute();

        return $stmt->fetchAll();
    }
}