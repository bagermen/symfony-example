<?php

namespace App\Stats;

use App\Entity\County;
use App\Entity\Tax;
use App\Entity\TaxIncome;
use Doctrine\ORM\EntityManagerInterface;
use App\Helpers;
use App\Import\Parser\Csv\Iterator as CsvIterator;
use App\Import\Parser\Excel\Iterator as ExcelIterator;
use App\Import\Parser\Iterator as FileIterator;
use App\Repository\CountyRepository;
use App\Repository\TaxRepository;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;

/**
 * Loads data into database
 */
class DataLoader
{
    use Helpers;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Main load data method
     * @param string $file
     * @param string $type
     */
    public function loadData(string $file, $type = 'csv'):void
    {
        if ($type == 'csv') {
            $iterator = new CsvIterator($file, ['offset' => 1]);
        } else if ($type == 'excel') {
            $iterator = new ExcelIterator($file, ['offset' => 1]);
        } else {
            return;
        }

        $this->saveData(new FileIterator($iterator));
    }

    /**
     * Saves data to database
     * @param \OuterIterator $iterator
     */
    protected function saveData(\OuterIterator $iterator)
    {
        $added = 0;

        $this->em->beginTransaction();

        /**
         * @var CountyRepository
         */
        $countyRep = $this->em->getRepository(County::class);

        /**
         * @var TaxRepository
         */
        $taxRep = $this->em->getRepository(Tax::class);

        try {
            foreach ($iterator as $row) {
                try {
                    $taxIncome = new TaxIncome();
                    if (count($row) == 3 && $row[0] && $row[1] && $row[2]) {
                        // these values are cached inside doctrine
                        $county = $countyRep->findOneBy(['name' => $row[0]]);
                        $tax = $taxRep->findOneBy(['code' => $row[1]]);

                        if ($iterator->getInnerIterator() instanceof ExcelIterator) {
                            $row[2] = $this->dateToStr(SharedDate::excelToDateTimeObject($row[2]));
                        }

                        if ($county && $tax && $this->strToDate($row[2])) {
                            $taxIncome->setCounty($county);
                            $taxIncome->setTax($tax);
                            $taxIncome->setDate($this->strToDate($row[2]));
                            $this->em->persist($taxIncome);
                            ++$added;
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }

                if ($added > 20) {
                    $this->em->flush();
                    $this->em->getUnitOfWork()->clear(TaxIncome::class);
                }
            }

            if ($added > 0) {
                $this->em->flush();
            }
        } catch (\Exception $e) {
            $this->em->rollback();
        }

        $this->em->commit();
        $this->em->clear();
    }
}