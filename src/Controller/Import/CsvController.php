<?php

namespace App\Controller\Import;

use App\Entity\TaxIncome;
use App\Export\Csv;
use App\Stats\DataLoader;
use App\Stats\TaxGenerate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CsvController extends AbstractController
{
    /**
     * Export CSV data
     */
    public function getAction(TaxGenerate $generate, Csv $csvGenerator)
    {
        $data = $generate->generate();
        $file = $csvGenerator->generate($data);

        $response = new BinaryFileResponse(
            $file,
            Response::HTTP_OK,
            ['Content-type' => 'text/csv']
        );

        $response->setContentDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, "taxIncome.csv");
        $response->deleteFileAfterSend(true);

        return $response;
    }

    /**
     * Load CSV data
     */
    public function postAction(DataLoader $loader, Request $request, EntityManagerInterface $entityManager)
    {
        try {
            /**
             * @var \Symfony\Component\HttpFoundation\File\UploadedFile
             */
            $file = $request->files->get('filecsv');

            $entityManager->getRepository(TaxIncome::class)->removeAll();
            $loader->loadData($file->getPathname(), 'csv');
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->redirectToRoute('index');
    }
}
