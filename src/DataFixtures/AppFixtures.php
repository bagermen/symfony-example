<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Country;
use App\Entity\State;
use App\Entity\County;
use App\Entity\Tax;
use App\Entity\TaxCounty;
use App\Entity\TaxIncome;
use App\Helpers;

/**
 * Load test data to database
 */
class AppFixtures extends Fixture
{
    use Helpers;

    /**
     * Main method
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadCountries($manager);
        $this->loadStates($manager);
        $this->loadCounty($manager);
        $this->loadTaxes($manager);
        $this->loadTaxCounty($manager);
        $this->loadTaxIncome($manager);
    }

    private function loadTaxIncome(ObjectManager $manager)
    {
        $offset = 0;
        $limit = 10;
        $rep = $manager->getRepository(TaxCounty::class);
        $firstDay = new \DateTime('first day of this month');
        $lastDay = new \DateTime('last day of this month');

        while (true) {
            $taxCounties = $rep->findBy([], null, $limit, $offset);

            if (!count($taxCounties)) {
                break;
            }
            $offset += $limit;

            foreach ($taxCounties as $taxCounty) {
                $num = rand(0, 10);

                for ($i = -1; $i < $num; $i++) {
                    $taxIncome = new TaxIncome();
                    $taxIncome
                        ->setTax($taxCounty->getTax())
                        ->setCounty($taxCounty->getCounty())
                        ->setDate($this->randomDateInRange($firstDay, $lastDay));
                    $manager->persist($taxIncome);
                }
            }
            $manager->flush();
            $manager->clear();
        }
    }

    /**
     * Generate and load taxes per county
     */
    private function loadTaxCounty(ObjectManager $manager)
    {
        $offset = 0;
        $limit = 10;
        $repTaxes = $manager->getRepository(Tax::class);
        $rep = $manager->getRepository(County::class);

        $taxes = $repTaxes->findAll();

        while (true) {
            $counties = $rep->findBy([], null, $limit, $offset);

            if (!count($counties)) {
                break;
            }
            $offset += $limit;

            foreach ($counties as $county) {
                $num = rand(-1, 8);
                $amount = rand(50, 100000);

                for ($i=-1;$i<$num;$i++) {
                    $taxCounty = new TaxCounty();
                    $taxCounty->setTax($taxes[$i+1])->setCounty($county)->setAmount($amount);
                    $manager->persist($taxCounty);
                }
            }
            $manager->flush();
        }
        $manager->clear();
    }

    /**
     * Generate base taxes
     */
    private function loadTaxes(ObjectManager $manager)
    {
        foreach ($this->getTaxList() as $taxCode) {
            $tax = new Tax();
            $tax->setCode($taxCode);
            $manager->persist($tax);
        }

        $manager->flush();
        $manager->clear();
    }

    /**
     * Return list of taxes
     */
    private function getTaxList()
    {
        return ['TAX1', 'TAX2', 'TAX3', 'TAX4', 'TAX5', 'TAX6', 'TAX7', 'TAX8', 'TAX9'];
    }

    /**
     * Generate and load county
     */
    private function loadCounty(ObjectManager $manager)
    {
        foreach ($this->getCountryList() as $items) {
            $rep = $manager->getRepository(State::class);
            $states = $rep->findByCountries(array_column($items, 'code'));

            foreach ($states as $state) {
                $num = rand(1, 8);

                for ($i = 0; $i < $num; $i++) {
                    $countyName = $state->getName() . '.' . $i;
                    $county = new County();
                    $county->setName($countyName)->setState($state);
                    $manager->persist($county);
                }
            }

            $manager->flush();
            $manager->clear();
        }
    }

    /**
     * Generate and load states
     */
    private function loadStates(ObjectManager $manager)
    {
        foreach ($this->getCountryList() as $items) {
            $rep = $manager->getRepository(Country::class);
            $countries = $rep->findByCodes(array_column($items, 'code'));
            $countryMap = [];

            foreach($countries as $country) {
                $countryMap[$country->getCode()] = $country;
            }

            foreach ($items as $item) {
                if (!empty($countryMap[$item['code']])) {
                    for ($i = 0; $i < 5; $i++) {
                        $stateName = $item['code'] . '.' . $i;
                        $state = new State();

                        $state->setName($stateName)->setCountry($countryMap[$item['code']]);
                        $manager->persist($state);
                    }
                }
            }
            $manager->flush();
            $manager->clear();
        }
    }

    /**
     * Fill countries
     */
    private function loadCountries(ObjectManager $manager)
    {
        foreach ($this->getCountryList() as $items) {
            foreach ($items as $item) {
                $country = new Country();
                $country->setCode($item['code'])->setName($item['name']);
                $manager->persist($country);
            }
            $manager->flush();
            $manager->clear();
        }
    }

    private function getCountryList()
    {
        $list = <<<LIST
[
{"name": "Afghanistan", "code": "AF"},
{"name": "Åland Islands", "code": "AX"},
{"name": "Albania", "code": "AL"},
{"name": "Algeria", "code": "DZ"},
{"name": "American Samoa", "code": "AS"},
{"name": "AndorrA", "code": "AD"},
{"name": "Angola", "code": "AO"},
{"name": "Anguilla", "code": "AI"},
{"name": "Antarctica", "code": "AQ"},
{"name": "Antigua and Barbuda", "code": "AG"},
{"name": "Argentina", "code": "AR"},
{"name": "Armenia", "code": "AM"},
{"name": "Aruba", "code": "AW"},
{"name": "Australia", "code": "AU"},
{"name": "Austria", "code": "AT"},
{"name": "Azerbaijan", "code": "AZ"},
{"name": "Bahamas", "code": "BS"},
{"name": "Bahrain", "code": "BH"}
]
LIST;

        $dataPiece = [];
        foreach(json_decode($list, true) as $country) {
            $dataPiece[] = $country;

            if (count($dataPiece) == 10) {
                yield $dataPiece;

                $dataPiece = [];
            }
        }

        if (count($dataPiece)) {
            yield $dataPiece;
        }
    }
}
