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
            'meta'          => [
                'robots'  => 'index, follow',
                'desc'    => 'Développeur web freelance à Aix-en-provence et Toulon, je maîtrise plusieurs langages web (HTML, CSS, JS, PHP), CMS (Prestashop, Wordpress) et framework (Symfony, Silex, ...) me permettant de gérer tout type de projet sur-mesure ou non.'
            ],
            'core_class'    => 'app-core--home',
            'bg_animated'   => true,
            'form_contact'  => $form_contact->createView()
        ]);
    }
}
