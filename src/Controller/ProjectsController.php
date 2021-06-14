<?php

namespace App\Controller;

// Entities
use App\Entity\Project;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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

        // Add <br> to description
        if (!is_null($project)) {
            $project->setDescription(nl2br($project->getDescription()));
        }

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

            return $this->render('projects/single.html.twig', [
                'meta' => [
                  'title' => 'Projets / ' . $project->getName(),
                  'desc'  => $project->getDescription()
                ],
                'project' => $project,
            ]);
        }
    }
}
