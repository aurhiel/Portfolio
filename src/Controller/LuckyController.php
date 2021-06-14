<?php

namespace App\Controller;

// Components
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
  * Require ROLE_ADMIN for *every* controller method in this class.
  *
  * @IsGranted("ROLE_ADMIN")
  */
class LuckyController extends AbstractController
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
