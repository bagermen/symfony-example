<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;

/**
 * List controller
 */
class ListController extends AbstractController
{
    /**
     * Return list of countries
     */
    public function countries(SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Country::class);

        try {
            $response = $repository->findAll();

            // it's better to use fosRestBundle with sensioBundle but Symfony 5 doesn't support them yet

            return new Response(
                $serializer->serialize($response, 'json'),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
        } catch (Exception $error) {
            throw new LogicException('Some error occuerd');
        }
    }
}
