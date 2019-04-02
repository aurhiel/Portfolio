<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// Entities
use App\Entity\Contact;

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
    }

    /**
     * @Route("/{page_slug}.html", name="static_pages")
     */
    public function index($page_slug)
    {
        $contact      = new Contact();
        $form_contact = $this->createForm(ContactType::class, $contact, [
            'action' => $this->generateUrl('contact')
        ]);

        if(isset($this->pages[$page_slug])) {
            $page = $this->pages[$page_slug];
            // Pushing form for contact
            $page['data']['form_contact'] = $form_contact->createView();

            return $this->render($page['template'], $page['data']);
        } else {
            return $this->redirectToRoute('dashboard');
        }
    }
}
