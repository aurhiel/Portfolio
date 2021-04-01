<?php

namespace App\Controller;

// Entities
use App\Entity\Quote;
use App\Entity\Invoice;

// Forms
use App\Form\QuoteType;
use App\Form\InvoiceType;

use App\Service\FileUploader;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
  * Require ROLE_ADMIN for *every* controller method in this class.
  *
  * @IsGranted("ROLE_ADMIN")
  */
class DashboardAccountingController extends AbstractController
{
    /**
     * @Route("/dashboard/compta/devis/{year}", name="dashboard_accounting_quotes", defaults={"year"=null})
     */
    public function accounting_quotes($year, Request $request, FileUploader $fileUploader)
    {
        $em = $this->getDoctrine()->getManager();

        // 1) Build the form
        $entity = new Quote();
        $form = $this->createForm(QuoteType::class, $entity, [
            'action' => $this->generateUrl('dashboard_accounting_quotes', [ 'year' => $year ])
        ]);

        // 2) Handle form
        $form->handleRequest($request);
        $return = array();

        // 3) Save !
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $documentFile */
            $documentFile = $form->get('document')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($documentFile) {
                $documentFileName = $fileUploader->upload($documentFile, '/quotes');
                $entity->setDocumentFilename($documentFileName);
            }

            $em->persist($entity);

            // 4) Try to save (flush) or clear
            try {
                // Flush OK !
                $em->flush();

                $return = array(
                    'query_status'    => 1,
                    'message_status'  => 'Sauvegarde effectuée avec succès.',
                    'id_entity'       => $entity->getId()
                );

                // Clear/reset form
                $entity = new Quote();
                $form = $this->createForm(QuoteType::class, $entity);
            } catch (\Exception $e) {
                // Something goes wrong
                $em->clear();

                $return = array(
                    'query_status'    => 0,
                    'exception'       => $e->getMessage(),
                    'message_status'  => 'Un problème est survenu lors de la sauvegarde du devis.'
                );
            }

            // 5) Set flash message
            $request->getSession()->getFlashBag()->add(
                (($return['query_status'] == 1) ? 'success' : 'error'),
                $return['message_status']
            );
        }

        // Retrieve entities
        $r_entity = $em->getRepository(Quote::class);
        $entities = (!is_null($year) && !empty((int)$year)) ? $r_entity->findAllByYear($year) : $r_entity->findAll();

        // Retrieve years based on quotes list
        $quotes_years = array();
        $years_signed = $r_entity->findAllYears();
        foreach ($years_signed as $year_signed) {
          $curr_year = (int)$year_signed['year_signed'];
          if (!in_array($curr_year, $quotes_years))
              $quotes_years[] = $curr_year;
        }

        return $this->render('dashboard/accounting/quotes.html.twig', [
            'form'          => $form->createView(),
            'quotes'        => $entities,
            'quotes_years'  => $quotes_years,
            'filters'       => [
                'year' => $year
            ]
        ]);
    }

    /**
     * @Route("/dashboard/compta/devis/delete/{id}", name="dashboard_accounting_quotes_delete")
     */
    public function accounting_quotes_delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve item to delete
        $repo   = $em->getRepository(Quote::class);
        $entity = $repo->findOneById($id);

        if ($entity !== null) {
            $em->remove($entity);
            $em->flush();

            // TODO: Delete quote document file
        } else {
            $request->getSession()->getFlashBag()->add('error',
              'Le devis avec pour ID: <b>' . $id . '</b> n\'existe pas en base de données.');
        }

        // No direct access
        return $this->redirectToRoute('dashboard_accounting_quotes');
    }

    /**
     * @Route("/dashboard/compta/factures/{year}", name="dashboard_accounting_invoices", defaults={"year"=null})
     */
    public function accounting_invoices($year, Request $request, FileUploader $fileUploader)
    {
        $em = $this->getDoctrine()->getManager();

        // 1) Build the form
        $entity = new Invoice();
        $form = $this->createForm(InvoiceType::class, $entity, [
            'action' => $this->generateUrl('dashboard_accounting_invoices', [ 'year' => $year ])
        ]);

        // 2) Handle form
        $form->handleRequest($request);
        $return = array();

        // 3) Save !
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $documentFile */
            $documentFile = $form->get('document')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($documentFile) {
                $documentFileName = $fileUploader->upload($documentFile, '/invoices');
                $entity->setDocumentFilename($documentFileName);
            }

            $em->persist($entity);

            // 4) Try to save (flush) or clear
            try {
                // Flush OK !
                $em->flush();

                $return = array(
                    'query_status'    => 1,
                    'message_status'  => 'Sauvegarde effectuée avec succès.',
                    'id_entity'       => $entity->getId()
                );

                // Clear/reset form
                $entity = new Invoice();
                $form = $this->createForm(InvoiceType::class, $entity);
            } catch (\Exception $e) {
                // Something goes wrong
                $em->clear();

                $return = array(
                    'query_status'    => 0,
                    'exception'       => $e->getMessage(),
                    'message_status'  => 'Un problème est survenu lors de la sauvegarde de la facture.'
                );
            }

            // 5) Set flash message
            $request->getSession()->getFlashBag()->add(
                (($return['query_status'] == 1) ? 'success' : 'error'),
                $return['message_status']
            );
        }

        // Retrieve entities
        $r_entity = $em->getRepository(Invoice::class);
        $entities = (!is_null($year) && !empty((int)$year)) ? $r_entity->findAllByYear($year) : $r_entity->findAll();

        // Retrieve years based on invoices list
        $invoices_years = array();
        $years_paid = $r_entity->findAllYears();
        foreach ($years_paid as $year_paid) {
          $curr_year = (int)$year_paid['year_paid'];
          if (!in_array($curr_year, $invoices_years))
              $invoices_years[] = $curr_year;
        }

        return $this->render('dashboard/accounting/invoices.html.twig', [
            'form'            => $form->createView(),
            'invoices'        => $entities,
            'invoices_years'  => $invoices_years,
            'filters'         => [
                'year' => $year
            ]
        ]);
    }

    /**
     * @Route("/dashboard/compta/factures/delete/{id}", name="dashboard_accounting_invoices_delete")
     */
    public function accounting_invoices_delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve item to delete
        $repo   = $em->getRepository(Invoice::class);
        $entity = $repo->findOneById($id);

        if ($entity !== null) {
            $em->remove($entity);
            $em->flush();

            // TODO: Delete invoice document file
        } else {
            $request->getSession()->getFlashBag()->add('error',
              'La facture avec pour ID: <b>' . $id . '</b> n\'existe pas en base de données.');
        }

        // No direct access
        return $this->redirectToRoute('dashboard_accounting_invoices');
    }

    /**
     * @Route("/dashboard/compta/livres-recette/{year}", name="dashboard_accounting_recipe_books", defaults={"year"=null})
     */
    public function accounting_recipe_books($year)
    {
        $em         = $this->getDoctrine()->getManager();
        $r_invoice  = $em->getRepository(Invoice::class);
        $r_quote    = $em->getRepository(Quote::class);

        // Retrieve years based on invoices list
        $invoices_years = array();
        $years_paid     = $r_invoice->findAllYears();
        foreach ($years_paid as $year_paid) {
          $curr_year = (int)$year_paid['year_paid'];
          if (!in_array($curr_year, $invoices_years))
              $invoices_years[$curr_year] = $curr_year;
        }

        // Retrieve & check $year
        $year = ((is_null($year)) ? end($invoices_years) : ((isset($invoices_years[$year])) ? $year : null));

        // Retrieve invoices & quotes
        $invoices = (!is_null($year) && !empty((int)$year)) ? $r_invoice->findAllByYear($year) : array();
        $quotes   = (!is_null($year) && !empty((int)$year)) ? $r_quote->findAllByYear($year) : array();

        // Loop on invoices to retrieve some data
        $turnovers_clients  = array();
        $turnovers_months   = array();
        $quotes             = array();
        $total_turnover     = 0;
        foreach ($invoices as $inv) {
            $quote  = $inv->getQuote();
            $client = $quote->getClient();
            $id_client = $client->getId();
            $inv_month = (int)$inv->getDatePaid()->format('m');

            // Create initial months turnovers
            if (!isset($turnovers_months[$inv_month]))
              $turnovers_months[$inv_month] = 0;

            // Retrieve quote item
            if (!isset($quotes[$quote->getId()]))
              $quotes[$quote->getId()] = $quote;

            // Create initial client turnover (= invoices total amount)
            if (!isset($turnovers_clients[$id_client]))
                $turnovers_clients[$id_client] = array('client' => $client, 'invoices_amount' => 0);

            // Update client invoices amount
            $turnovers_clients[$id_client]['invoices_amount'] += $inv->getAmount();

            // Update total turnover & turnovers by months
            $total_turnover += $inv->getAmount();
            $turnovers_months[$inv_month] += $inv->getAmount();
        }

        // Retrieve average monthly turnover TODO current year = less months
        $monthly_turnovers = $total_turnover / 12;

        return $this->render('dashboard/accounting/recipe-books.html.twig', [
            'invoices'      => $invoices,
            'quotes'        => $quotes,
            'current_year'  => $year,
            'years'         => $invoices_years,
            'quotes'        => $quotes,
            // Stats
            'turnovers_clients' => $turnovers_clients,
            'turnovers_months'  => $turnovers_months,
            'monthly_turnover'  => $monthly_turnovers,
            'total_turnover'    => $total_turnover,
        ]);
    }
}
