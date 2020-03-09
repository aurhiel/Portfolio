<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// Entities
use App\Entity\Contact;
use App\Entity\Testimonial;

// Forms
use App\Form\ContactType;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        // Create contact form
        $contact      = new Contact();
        $form_contact = $this->createForm(ContactType::class, $contact, [
            'action' => $this->generateUrl('contact')
        ]);

        // Retrieve testimonials
        $r_testimonials = $em->getRepository(Testimonial::class);
        $testimonials   = $r_testimonials->findAll(true);

        return $this->render('home.html.twig', [
            'meta'          => [
                'robots'  => 'index, follow',
                'desc'    => 'Développeur web freelance à Aix-en-provence et Toulon, je maîtrise plusieurs langages web (HTML, CSS, JS, PHP), CMS (Prestashop, Wordpress) et framework (Symfony, Silex, ...) me permettant de gérer tout type de projet sur-mesure ou non.'
            ],
            'core_class'    => 'app-core--home',
            'bg_animated'   => true,
            'form_contact'  => $form_contact->createView(),
            'testimonials'  => $testimonials
        ]);
    }
}
