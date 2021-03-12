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

class DashboardAccountingController extends AbstractController
{
    /**
     * @Route("/dashboard/compta/devis", name="dashboard_accounting_quotes")
     */
    public function accounting_quotes(Request $request, FileUploader $fileUploader)
    {
        $em = $this->getDoctrine()->getManager();

        // 1) Build the form
        $entity = new Quote();
        $form = $this->createForm(QuoteType::class, $entity, [
            'action' => $this->generateUrl('dashboard_accounting_quotes')
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
        $entities = $r_entity->findAll();

        return $this->render('dashboard/accounting/quotes.html.twig', [
            'form'    => $form->createView(),
            'quotes'  => $entities
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
     * @Route("/dashboard/compta/factures", name="dashboard_accounting_invoices")
     */
    public function accounting_invoices(Request $request, FileUploader $fileUploader)
    {
        $em = $this->getDoctrine()->getManager();

        // 1) Build the form
        $entity = new Invoice();
        $form = $this->createForm(InvoiceType::class, $entity, [
            'action' => $this->generateUrl('dashboard_accounting_invoices')
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
        $entities = $r_entity->findAll();

        return $this->render('dashboard/accounting/invoices.html.twig', [
            'form'      => $form->createView(),
            'invoices'  => $entities
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
     * @Route("/dashboard/compta/livres-recette", name="dashboard_accounting_recipe_books")
     */
    public function accounting_recipe_books()
    {
        exit('recipe books. // TODO');
    }
}
