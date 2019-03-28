<?php
// src/Controller/LuckyController.php
namespace App\Controller;

// Components
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends Controller
{
    /**
     * @Route("/boo/{max}", name="app_lucky_number")
     */
    public function number($max)
    {
        $number = random_int(1, $max);

        return $this->render('lucky.html.twig', array('number' => $number));
    }
}
