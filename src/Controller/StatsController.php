<?php

namespace App\Controller;

use App\Entity\Country;
use App\Stats\TaxIncome;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Main Controller
 */
class StatsController extends AbstractController
{
    public function statistics($code, TaxIncome $taxIncome, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $repository = $entityManager->getRepository(Country::class);
        /** @var Country */
        $country = $repository->find($code);
        $firstDay = new \DateTime('first day of this month');
        $lastDay = new \DateTime('last day of this month');

        try {
            if ($country) {
                $response = [
                    'country_tax' => $taxIncome->getCountryTax($country, $firstDay, $lastDay),
                    'avg_amount_per_state' => $taxIncome->getAvgAmountPerState($country, $firstDay, $lastDay),
                    'avg_tax_rate' => $taxIncome->getAvgTaxRate($country, $firstDay, $lastDay),
                    'stats' => $taxIncome->getStateTaxRateStats($country, $firstDay, $lastDay)
                ];

                return new Response(
                    $serializer->serialize($response, 'json'),
                    Response::HTTP_OK,
                    ['Content-type' => 'application/json']
                );
            }
        } catch (Exception $error) {
            throw new LogicException('Some error occuerd');
        }

        return $this->json(new \stdClass());
    }
}
