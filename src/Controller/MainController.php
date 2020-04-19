<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Main Controller
 */
class MainController extends AbstractController
{
    /**
     * Initial view
     */
    public function index()
    {
        return $this->render('main/index.twig');
    }
}