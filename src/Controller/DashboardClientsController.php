<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// Entities
use App\Entity\Client;

// Forms
use App\Form\ClientType;

// Services
use App\Service\FileUploader;

/**
  * Require ROLE_ADMIN for *every* controller method in this class.
  *
  * @IsGranted("ROLE_ADMIN")
  */
class DashboardClientsController extends AbstractController
{
    /**
     * @Route("/dashboard/clients/{id}", name="dashboard_clients", defaults={"id"=null})
     */
    public function index($id, Request $request, FileUploader $fileUploader)
    {
        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Client::class);

        // 1) Build the form
        $client = (!is_null($id)) ? $repo->findOneById($id) : new Client();
        $form = $this->createForm(ClientType::class, $client);

        // 2) Handle form
        $form->handleRequest($request);
        $data = array();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $documentFile */
            $logoFile = $form->get('logo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($logoFile) {
                $logoFileName = $fileUploader->upload($logoFile, '/clients');
                $client->setLogoFilename($logoFileName);
            }

            // 3) Save !
            $em->persist($client);

            // 4) Try to save (flush) or clear
            try {
                // Flush OK !
                $em->flush();

                $data = array(
                    'query_status'    => 1,
                    'message_status'  => 'Sauvegarde effectuée avec succès.',
                    'id_entity'       => $client->getId()
                );

                // Clear/reset form
                $client = new Client();
                $form = $this->createForm(ClientType::class, $client);
            } catch (\Exception $e) {
                // Something goes wrong
                $em->clear();

                $data = array(
                    'query_status'    => 0,
                    'exception'       => $e->getMessage(),
                    'message_status'  => 'Un problème est survenu lors de la sauvegarde du client.'
                );
            }

            // 5) Set flash message
            $request->getSession()->getFlashBag()->add(
                (($data['query_status'] == 1) ? 'success' : 'error'),
                $data['message_status']
            );

            // Redirect on edit to clear edit form
            if (!is_null($id))
                return $this->redirectToRoute('dashboard_clients');
        }

        // Retrieve clients
        $r_clients  = $em->getRepository(Client::class);
        $clients    = $r_clients->findAll();

        return $this->render('dashboard/clients.html.twig', [
          'form'    => $form->createView(),
          'clients' => $clients
        ]);
    }

    /**
     * @Route("/dashboard/clients/delete/{id}", name="dashboard_clients_delete")
     */
    public function clients_delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve item to delete
        $repo   = $em->getRepository(Client::class);
        $entity = $repo->findOneById($id);

        if ($entity !== null) {
            $em->remove($entity);
            $em->flush();
        } else {
            $request->getSession()->getFlashBag()->add('error',
              'Le client avec pour ID: <b>' . $id . '</b> n\'existe pas en base de données.');
        }

        // No direct access
        return $this->redirectToRoute('dashboard_clients');
    }
}
