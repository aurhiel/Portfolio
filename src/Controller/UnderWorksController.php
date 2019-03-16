<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UnderWorksController extends AbstractController
{
    /**
     * @Route("/en-construction", name="under_works")
     */
    public function index()
    {
        return $this->render('under-works.html.twig', [
            'core_class'  => 'app-core--under-works'
        ]);
    }
}
