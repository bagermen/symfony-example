<?php
namespace App\Stats;

use App\Entity\TaxCounty;
use Doctrine\ORM\EntityManagerInterface;
use App\Helpers;

/**
 * Generate example dummpy income
 */
class TaxGenerate
{
    use Helpers;

    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Generate list of dummy tax incomes
     * @return array
     */
    public function generate(): array
    {
        $offset = 0;
        $limit = 10;
        $rep = $this->em->getRepository(TaxCounty::class);
        $firstDay = new \DateTime('first day of this month');
        $lastDay = new \DateTime('last day of this month');

        $data = [];

        while (true) {
            $taxCounties = $rep->findBy([], null, $limit, $offset);

            if (!count($taxCounties)) {
                break;
            }
            $offset += $limit;

            foreach ($taxCounties as $taxCounty) {
                $num = rand(0, 10);

                for ($i = -1; $i < $num; $i++) {
                    $data[] = [
                        'tax' => $taxCounty->getTax()->getCode(),
                        'county' => $taxCounty->getCounty()->getName(),
                        'date' => $this->randomDateInRange($firstDay, $lastDay)
                    ];
                }
            }
            $this->em->clear();
        }

        return $data;
    }
}