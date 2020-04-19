<?php

namespace App\Controller\Import;

use App\Entity\TaxIncome;
use App\Export\Excel;
use App\Stats\DataLoader;
use App\Stats\TaxGenerate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;

class ExcelController extends AbstractController
{
    /**
     * Export Excel data
     */
    public function getAction(TaxGenerate $generate, Excel $excelGenerator)
    {
        $data = $generate->generate();
        $file = $excelGenerator->generate($data);

        $response = new BinaryFileResponse(
            $file,
            Response::HTTP_OK,
            ['Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );

        $response->setContentDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, "taxIncome.xlsx");
        $response->deleteFileAfterSend(true);

        return $response;
    }

    /**
     * Load Excel data.
     * @TODO Excel should not be processed by direct calls because it takes a lot of time to process it. It's better to use a daemon for the purpose
     */
    public function postAction(DataLoader $loader, Request $request, EntityManagerInterface $entityManager)
    {
        set_time_limit(0); // we have to set this parameter because Excel requires a lot of time for insert

        try {
            /**
             * @var \Symfony\Component\HttpFoundation\File\UploadedFile
             */
            $file = $request->files->get('fileexcel');

            $entityManager->getRepository(TaxIncome::class)->removeAll();
            $loader->loadData($file->getPathname(), 'excel');
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->redirectToRoute('index');
    }
}
