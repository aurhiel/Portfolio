<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// Entities
use App\Entity\Project;
use App\Entity\ProjectSpec;
use App\Entity\Image;

// Forms
use App\Form\ProjectType;
use App\Form\ProjectSpecType;

// Services
use App\Service\FileUploader;

/**
  * Require ROLE_ADMIN for *every* controller method in this class.
  *
  * @IsGranted("ROLE_ADMIN")
  */
class DashboardProjectsController extends AbstractController
{
    /**
     * @Route("/dashboard/projets/{id}", name="dashboard_projects", defaults={"id"=null})
     */
    public function index($id, Request $request, FileUploader $fileUploader): Response
    {
        $em         = $this->getDoctrine()->getManager();
        $r_projects = $em->getRepository(Project::class);

        // 1) Build the project's spec form
        $project_spec = new ProjectSpec();
        $form_spec = $this->createForm(ProjectSpecType::class, $project_spec);
        //    & build the project form
        $project = (!is_null($id)) ? $r_projects->findOneById($id) : new Project();
        $form_project = $this->createForm(ProjectType::class, $project);

        // 2) Handle spec. & project forms
        $form_spec->handleRequest($request);
        $form_project->handleRequest($request);
        $data = array();

        // Spec form submit
        if ($form_spec->isSubmitted() && $form_spec->isValid()) {
            // 3) Save !
            $em->persist($project_spec);

            // 4) Try to save (flush) or clear
            try {
                // Flush OK !
                $em->flush();

                $data = array(
                    'query_status'    => 1,
                    'message_status'  => 'Sauvegarde de la caractéristique effectuée avec succès.',
                    'id_entity'       => $project_spec->getId()
                );

                // Clear/reset form
                $project_spec = new ProjectSpec();
                $form_spec = $this->createForm(ProjectSpecType::class, $project_spec);
            } catch (\Exception $e) {
                // Something goes wrong
                $em->clear();

                $data = array(
                    'query_status'    => 0,
                    'exception'       => $e->getMessage(),
                    'message_status'  => 'Un problème est survenu lors de la sauvegarde de la caractéristique.'
                );
            }

            // Redirect on edit to clear edit form
            return $this->redirectToRoute('dashboard_projects');
        }

        // Spec form submit
        if ($form_project->isSubmitted() && $form_project->isValid()) {
            /** @var UploadedFile $documentFile */
            $illusFile = $form_project->get('illustration')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($illusFile) {
                $illusFileName = $fileUploader->upload($illusFile, '/projects/illustrations');
                $project->setIllustrationFilename($illusFileName);
            }

            // 3) Save !
            $em->persist($project);

            // 4) Try to save (flush) or clear
            try {
                // Flush OK !
                $em->flush();

                // Upload project screenshots only if project was correctly saved
                //  & remove previous screenshots
                $screenshots = $form_project->get('screenshots')->getData();
                if (!empty($screenshots)) {
                    // Delete previous screenshots
                    $old_screens = $project->getScreenshots();
                    foreach ($old_screens as $screen) {
                        $project->removeScreenshot($screen);
                        $em->remove($screen);
                    }

                    // Upload & add screenshots
                    foreach ($screenshots as $screenData) {
                        // Upload new advert image
                        $imageFileName = $fileUploader->upload($screenData, '/projects/screenshots');

                        // Create & persist new Image entity after upload
                        $image = new Image();
                        $image->setFilename($imageFileName);

                        // Add new image to current advert
                        $project->addScreenshot($image);

                        $em->persist($image);
                        $em->flush();
                    }
                }

                $data = array(
                    'query_status'    => 1,
                    'message_status'  => 'Sauvegarde du projet effectuée avec succès.',
                    'id_entity'       => $project->getId()
                );

                // Clear/reset form
                $project = new Project();
                $form_project = $this->createForm(ProjectType::class, $project);
            } catch (\Exception $e) {
                // Something goes wrong
                $em->clear();

                $data = array(
                    'query_status'    => 0,
                    'exception'       => $e->getMessage(),
                    'message_status'  => 'Un problème est survenu lors de la sauvegarde du projet.'
                );
            }

            // Redirect on edit to clear edit form
            if (!is_null($id))
                return $this->redirectToRoute('dashboard_projects');
        }

        // 5) Set flash message if defined
        if (!empty($data)) {
            $request->getSession()->getFlashBag()->add(
                (($data['query_status'] == 1) ? 'success' : 'error'),
                $data['message_status']
            );
        }

        // Retrieve specs & projects
        $r_specs    = $em->getRepository(ProjectSpec::class);
        $specs      = $r_specs->findAll();
        $projects   = $r_projects->findAll();

        return $this->render('dashboard/projects.html.twig', [
            'form_spec'     => $form_spec->createView(),
            'form_project'  => $form_project->createView(),
            'projects'      => $projects,
            'project_specs' => $specs,
        ]);
    }

    /**
     * @Route("/dashboard/projets/delete/{id}", name="dashboard_projects_delete")
     */
    public function projects_delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve item to delete
        $repo   = $em->getRepository(Project::class);
        $entity = $repo->findOneById($id);

        if ($entity !== null) {
            $em->remove($entity);

            // TODO delete files

            $em->flush();
        } else {
            $request->getSession()->getFlashBag()->add('error',
              'Le project avec pour ID: <b>' . $id . '</b> n\'existe pas en base de données.');
        }

        // No direct access
        return $this->redirectToRoute('dashboard_projects');
    }

    /**
     * @Route("/dashboard/projets/caracteristiques/delete/{id}", name="dashboard_projects_specs_delete")
     */
    public function projects_specs_delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve item to delete
        $repo   = $em->getRepository(ProjectSpec::class);
        $entity = $repo->findOneById($id);

        if ($entity !== null) {
            $em->remove($entity);
            $em->flush();
        } else {
            $request->getSession()->getFlashBag()->add('error',
              'La caractéristique avec pour ID: <b>' . $id . '</b> n\'existe pas en base de données.');
        }

        // No direct access
        return $this->redirectToRoute('dashboard_projects');
    }
}
