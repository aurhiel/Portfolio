<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// Entities
use App\Entity\Client;
use App\Entity\Testimonial;
use App\Entity\CurryQSkill;

// Forms
use App\Form\ClientType;

/**
  * Require ROLE_ADMIN for *every* controller method in this class.
  *
  * @IsGranted("ROLE_ADMIN")
  */
class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index()
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    /**
     * @Route("/dashboard/clients", name="dashboard_clients")
     */
    public function clients(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // 1) Build the form
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client, [
            'action' => $this->generateUrl('dashboard_clients')
        ]);

        // 2) Handle form
        $form->handleRequest($request);
        $data = array();

        if ($form->isSubmitted() && $form->isValid()) {
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
     * @Route("/dashboard/temoignages", name="dashboard_testimonials")
     */
    public function testimonials(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve clients
        $r_clients  = $em->getRepository(Client::class);
        $clients    = $r_clients->findAll();

        // Handle submit new testimonial generation
        $id_client = (int) $request->request->get('testimonial-client');
        if ($id_client > 0 && isset($clients[$id_client])) {
            // Generate new testimonial (to complete by client)
            $testimonial = new Testimonial();
            $testimonial
                ->setClient($clients[$id_client])
                ->setIsActive(false)
                ->generateToken(new \DateInterval('P7D'));

            // Persist & flush client's testimonial
            $em->persist($testimonial);
            $em->flush();
        }

        $r_testimonials = $em->getRepository(Testimonial::class);
        $testimonials   = $r_testimonials->findAll();

        return $this->render('dashboard/testimonials.html.twig', [
          'clients'       => $clients,
          'testimonials'  => $testimonials
        ]);
    }

    /**
     * @Route("/dashboard/temoignages/toggle/{id}", name="dashboard_testimonials_toggle")
     */
    public function testimonial_toggle($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve clients
        $r_testimonials = $em->getRepository(Testimonial::class);
        $testimonial    = $r_testimonials->findOneById($id);

        // Toggle testimonial "isActive"
        $testimonial->setIsActive($request->request->get('toggle') === 'true');

        // Persist & flush client's testimonial
        $em->persist($testimonial);
        $em->flush();

        $data = array(
          'id'        => $testimonial->getId(),
          'is_active' => $testimonial->getIsActive()
        );

        if ($request->isXmlHttpRequest()) {
            return $this->json($data);
        } else {
            // No direct access
            return $this->redirectToRoute('dashboard');
        }
    }
}
