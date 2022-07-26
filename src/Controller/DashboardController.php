<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// Entities
// use App\Entity\Client;
use App\Entity\Testimonial;
use App\Entity\Quote;
use App\Entity\Invoice;

// Forms
// use App\Form\ClientType;

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
        $now        = new \DateTime();

        // Retrieve turnover (quotes, invoices & rest to pay) by years
        $quote_total_years    = $r_quote->findTotalAmountsGroupByYear();
        $invoice_total_years  = $r_invoice->findTotalAmountsGroupByYear();
        $turnover_years       = array();
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

        // Retrieve turnover by clients & over key stats.
        $quotes             = $r_quote->findAll();
        $turnovers_clients  = array();
        $total_turnover     = 0;
        foreach ($quotes as $quote) {
            $client = $quote->getClient();
            $id_client = $client->getId();
            if (!isset($turnovers_clients[$id_client]))
                $turnovers_clients[$id_client] = array('client' => $client, 'quotes_amount' => 0, 'invoices_amount' => 0);

            // Update quotes & invoices amount
            $turnovers_clients[$id_client]['quotes_amount'] += $quote->getAmount();
            $turnovers_clients[$id_client]['invoices_amount'] += $quote->getInvoicesTotalAmount();

            // Update total turnover & turnovers by months
            $total_turnover += $quote->getInvoicesTotalAmount();
        }

        // Retrieve nb years of activity & monthly turnovers
        $nb_years_activity = (int)$now->format('Y') - array_key_first($turnover_years);
        $monthly_turnovers = $total_turnover / (($nb_years_activity * 12) + (int)$now->format('n'));

        // Retrieve testimonials
        $r_testimonials = $em->getRepository(Testimonial::class);
        $testimonials   = $r_testimonials->findAll();

        return $this->render('dashboard/index.html.twig', [
            'quotes'  => $quotes,
            'stats'   => [
              'turnover_years'    => $turnover_years,
              'turnover_clients'  => $turnovers_clients,
              'total_turnover'    => $total_turnover,
              'monthly_turnover'  => $monthly_turnovers,
              'nb_years_activity' => $nb_years_activity,
              'nb_testimonials'   => count($testimonials),
            ],
        ]);
    }
}
