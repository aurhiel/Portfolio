<?php

namespace App\Controller;

// Entities
use App\Entity\Contact;
use App\Entity\Project;
use App\Entity\Testimonial;

// Forms
use App\Form\ContactType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProjectsController extends AbstractController
{
    /**
     * @Route("/projets/{id}-{slug}", name="projects_single")
     */
    public function single($id, $slug, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve projects
        $r_projects = $em->getRepository(Project::class);
        $project = $r_projects->findOneById($id);

        // Retrieve testimonials to display link in menu
        $r_testimonials = $em->getRepository(Testimonial::class);
        $testimonials   = $r_testimonials->findAll(true);

        // Create contact form
        $contact      = new Contact();
        $form_contact = $this->createForm(ContactType::class, $contact, [
            'action' => $this->generateUrl('contact')
        ]);

        // Add <br> to description
        if (!is_null($project))
            $project->setDescription(nl2br($project->getDescription()));

        // JSON response or HTML display
        if ($request->isXmlHttpRequest()) {
            return $this->json($project);
        } else {
            if (!is_null($project)) {
                // Invalid $slug > redirect with correct one
                if ($slug != $project->getSlug()) {
                    return $this->redirectToRoute('projects_single', [
                        'id'    => $project->getId(),
                        'slug'  => $project->getSlug(),
                    ]);
                }
            } else {
                // Invalid $project or something else > redirect to homepage
                return $this->redirectToRoute('home');
            }

            $desc_simplified = preg_replace( "/\r|\n/", "", strip_tags($project->getDescription()));
            $add_etc = (strlen($desc_simplified) > 300);

            return $this->render('projects/single.html.twig', [
                'meta' => [
                    'robots'  => 'index, follow',
                    'title'   => 'Projets / ' . $project->getName(),
                    'desc'    => substr($desc_simplified, 0, 299) . ($add_etc ? '...' : '')
                ],
                'form_contact'  => $form_contact->createView(),
                'project'       => $project,
                'testimonials'  => $testimonials,
            ]);
        }
    }
}
