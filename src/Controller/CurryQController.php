<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CurryQController extends AbstractController
{
    /**
     * @Route("/curriculum-vitae.html", name="curriculum")
     */
    public function index()
    {
        return $this->render('curriculum-vitae.html.twig');
    }
}
