<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// Entities
use App\Entity\Client;
use App\Entity\Testimonial;

// Forms
// use App\Form\ClientType;

/**
  * Require ROLE_ADMIN for *every* controller method in this class.
  *
  * @IsGranted("ROLE_ADMIN")
  */
class DashboardTestimonialsController extends AbstractController
{
    /**
     * @Route("/dashboard/temoignages", name="dashboard_testimonials")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve clients
        $r_clients  = $em->getRepository(Client::class);
        $clients    = $r_clients->findAll();

        // Handle submit new testimonial generation
        $id_client = (int) $request->request->get('testimonial-client');
        if ($id_client > 0 && isset($clients[$id_client])) {
            $nb_days = max((int) $request->request->get('testimonial-nb-days'), 7);
            // Generate new testimonial (to complete by client)
            $testimonial = new Testimonial();
            $testimonial
                ->setClient($clients[$id_client])
                ->setSignType('both')
                ->setIsActive(false)
                ->generateToken(new \DateInterval('P' . $nb_days . 'D'));

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

    /**
     * @Route("/dashboard/temoignages/delete/{id}", name="dashboard_testimonials_delete")
     */
    public function testimonial_delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve item to delete
        $r_testimonials = $em->getRepository(Testimonial::class);
        $testimonial    = $r_testimonials->findOneById($id);

        if ($testimonial !== null) {
            $em->remove($testimonial);
            $em->flush();
        } else {
            $request->getSession()->getFlashBag()->add('error',
              'Le témoignage avec pour ID: <b>' . $id . '</b> n\'existe pas en base de données.');
        }

        // No direct access
        return $this->redirectToRoute('dashboard_testimonials');
    }
}
