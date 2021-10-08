<?php

namespace App\Controller;

// Entities
use App\Entity\Contact;
use App\Entity\Project;
use App\Entity\Testimonial;

// Forms
use App\Form\ContactType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SitemapController extends AbstractController
{
    /**
     * @Route("/plan-du-site.{_format}", name="sitemap")
     */
    public function index(Request $request): Response
    {
        $format = $request->getRequestFormat();
        $em = $this->getDoctrine()->getManager();
        $date_lastmod_default = '2021-10-08';

        // Retrieve hostname only for XML version
        $hostname = (($format == 'xml') ? $request->getSchemeAndHttpHost() : '');

        // Retrieve testimonials to display link in menu
        $r_testimonials = $em->getRepository(Testimonial::class);
        $testimonials   = $r_testimonials->findAll(true);

        // Create contact form
        $contact      = new Contact();
        $form_contact = $this->createForm(ContactType::class, $contact, [
            'action' => $this->generateUrl('contact')
        ]);

        // Retrieve static URLs
        $sm_urls = [
            [
                'label'   => 'Accueil',
                'loc'     => $hostname . $this->generateUrl('home'),
                'lastmod' => $date_lastmod_default
            ],
        ];

        // Retrieve projects to push in URLs array
        $r_projects = $em->getRepository(Project::class);
        $projects   = $r_projects->findAll();

        // Retrieve projects URLs
        if (!empty($projects)) {
            $sm_urls_projects = [];
            foreach ($projects as $project) {
                $sm_urls_projects[] = [
                    'label'   => $project->getName(),
                    'loc'     => $hostname . $this->generateUrl('projects_single', [ 'id' => $project->getId(), 'slug' => $project->getSlug() ]),
                    'lastmod' => $date_lastmod_default
                ];
            }

            $sm_urls[] = [
                'label'     => 'Projets',
                'children'  => $sm_urls_projects
            ];
        }

        // Push legal terms page AFTER projects pages
        $sm_urls[] = [
            'label'   => 'Mentions Légales',
            'loc'     => $hostname . $this->generateUrl('static_pages', [ 'page_slug' => 'mentions-legales' ]),
            'lastmod' => $date_lastmod_default
        ];

        return $this->render('sitemap.' . $format . '.twig', [
            'meta' => [
                'robots'  => 'index, follow',
                'title'   => 'Plan du site',
                // 'desc'    => ''
            ],
            'form_contact'          => $form_contact->createView(),
            'testimonials'          => $testimonials,
            'urls'                  => $sm_urls,
            'date_lastmod_default'  => $date_lastmod_default
        ]);
    }
}
