<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// Entities
use App\Entity\Client;
use App\Entity\Testimonial;
use App\Entity\Quote;
use App\Entity\Invoice;

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
        $em         = $this->getDoctrine()->getManager();
        $r_quote    = $em->getRepository(Quote::class);
        $r_invoice  = $em->getRepository(Invoice::class);

        // Retrieve turnover (quotes, invoices & rest to pay) by years
        $quote_total_years    = $r_quote->findTotalAmountsGroupByYear();
        $invoice_total_years  = $r_invoice->findTotalAmountsGroupByYear();
        $turnover_years = array();
        if (!empty($quote_total_years)) {
            foreach ($quote_total_years as $quote_year) {
                $curr_year = (int)$quote_year['year_signed'];
                if (!isset($turnover_years[$curr_year]))
                    $turnover_years[$curr_year] = array('quotes_amount' => 0, 'invoices_amount' => 0);

                $turnover_years[$curr_year]['quotes_amount'] = (float)$quote_year['total_amount'];
            }
        }
        if (!empty($invoice_total_years)) {
            foreach ($invoice_total_years as $invoice_year) {
                $curr_year = (int)$invoice_year['year_paid'];
                if (!isset($turnover_years[$curr_year]))
                    $turnover_years[$curr_year] = array('quotes_amount' => 0, 'invoices_amount' => 0);

                $turnover_years[$curr_year]['invoices_amount'] = (float)$invoice_year['total_amount'];
            }
        }

        // Retrieve turnover by clients
        $quotes = $r_quote->findAll();
        $turnovers_clients = array();
        foreach ($quotes as $quote) {
            $client = $quote->getClient();
            $id_client = $client->getId();
            if (!isset($turnovers_clients[$id_client]))
                $turnovers_clients[$id_client] = array('client' => $client, 'quotes_amount' => 0, 'invoices_amount' => 0);

            // Update quotes & invoices amount
            $turnovers_clients[$id_client]['quotes_amount'] += $quote->getAmount();
            $turnovers_clients[$id_client]['invoices_amount'] += $quote->getInvoicesTotalAmount();
        }

        dump($quotes, $turnovers_clients);

        return $this->render('dashboard/index.html.twig', [
            'stats' => [
              'turnover_years'    => $turnover_years,
              'turnover_clients'  => $turnovers_clients,
            ],
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
