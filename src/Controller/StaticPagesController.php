<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// Entities
use App\Entity\Contact;
use App\Entity\Testimonial;

// Forms
use App\Form\ContactType;

class StaticPagesController extends AbstractController
{

    private $pages = array();

    public function __construct()
    {
        // Raw pages data

        $owner = array(
            'firstname' => 'Aurélien',
            'lastname'  => 'Litti',
            'phone'     => '+33 6 95 06 40 91',
            'email'     => 'litti.aurelien@gmail.com'
        );

        $developer = $owner;

        $web_host = array(
            'name'        => 'OVH',
            'address'     => '2 rue Kellerman – BP 80157 – 59100 Roubaix – France',
            'social_form' => 'SAS (Société par actions simplifiée)',
            'phone'       => '+33 820 698 765'
        );



        // Pages list

        $this->pages['mentions-legales'] = array(
            'template'      => 'static-pages/legal-terms.html.twig',
            'data' => array(
                'meta' => array(
                    'title'   => 'Mentions Légales',
                    'robots'  => 'index, follow'
                ),
                'owner'     => $owner,
                'developer' => $developer,
                'web_host'  => $web_host
            )
        );

        $this->pages['carte-de-visite'] = array(
            'template'      => 'static-pages/business-card.html.twig',
            'data' => array(
                'meta' => array(
                    'title'   => 'Carte de Visite',
                    'robots'  => 'noindex, nofollow'
                ),
            )
        );
    }

    /**
     * @Route("/{page_slug}.html", name="static_pages")
     */
    public function index($page_slug)
    {
        $em = $this->getDoctrine()->getManager();

        // Generate contact form
        $contact      = new Contact();
        $form_contact = $this->createForm(ContactType::class, $contact, [
            'action' => $this->generateUrl('contact')
        ]);

        // Get testimonials repository
        $r_testimonials = $em->getRepository(Testimonial::class);

        if(isset($this->pages[$page_slug])) {
            $page = $this->pages[$page_slug];
            // Add some data on all pages expect business card page
            if ($page_slug != 'carte-de-visite') {
                // Pushing form for contact
                $page['data']['form_contact'] = $form_contact->createView();

                // Retrieve testimonials to display link in menu
                $page['data']['testimonials'] = $r_testimonials->findAll(true);
            }

            return $this->render($page['template'], $page['data']);
        } else {
            return $this->redirectToRoute('dashboard');
        }
    }
}
