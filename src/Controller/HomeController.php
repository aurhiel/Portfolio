<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// Entities
use App\Entity\Contact;

// Forms
use App\Form\ContactType;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $contact      = new Contact();
        $form_contact = $this->createForm(ContactType::class, $contact, [
            'action' => $this->generateUrl('contact')
        ]);

        return $this->render('home.html.twig', [
            'core_class'    => 'app-core--home',
            'bg_animated'   => true,
            'form_contact'  => $form_contact->createView()
        ]);
    }
}
