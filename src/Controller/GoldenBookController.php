<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// Entities
use App\Entity\Testimonial;

class GoldenBookController extends AbstractController
{
    /**
     * @Route("/livre-d-or/{token}", name="golden_book")
     */
    public function index($token, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve testimonial according to $token value
        $r_testimonials = $em->getRepository(Testimonial::class);
        $testimonial    = $r_testimonials->findOneByToken($token);

        if ($testimonial !== null && $testimonial->isTokenValid($token)) {
            $client = $testimonial->getClient();

            // 1) Handle form submit
            $r = $request->request;
            if ($r->get('testimonial-send') == 'exec' && !empty($r->get('testimonial-message'))) {
                // 2) Update testimonial
                $testimonial
                    ->setMessage($r->get('testimonial-message'))
                    ->setIp($request->getClientIp())
                    ->setAgent($request->server->get('HTTP_USER_AGENT'))
                    ->setDisplayNames(($r->get('testimonial-display-names') !== null))
                ;

                // 3) Save !
                $em->persist($testimonial);

                // 4) Try to save (flush) or clear
                try {
                    // Flush OK !
                    $em->flush();

                    $date = new \DateTime();
                    $date->setTimestamp($testimonial->getTokenExpiresAt());

                    $data = array(
                        'query_status'    => 1,
                        'message_status'  => 'Témoignage sauvegardé, il est disponible à la modification jusqu\'au <u>' . $date->format('l d F') . '</u>.',
                        'id_entity'       => $testimonial->getId()
                    );
                } catch (\Exception $e) {
                    // Something goes wrong
                    $em->clear();

                    $data = array(
                        'query_status'    => 0,
                        'exception'       => $e->getMessage(),
                        'message_status'  => 'Un problème est survenu lors de la sauvegarde du témoignage.'
                    );
                }

                // 5) Set flash message
                $request->getSession()->getFlashBag()->add(
                    (($data['query_status'] == 1) ? 'success' : 'error'),
                    $data['message_status']
                );
            }

            return $this->render('dashboard/golden-book.html.twig', [
              'client'      => $client,
              'testimonial' => $testimonial
            ]);
        } else {
            // $token is invalid > redirect to homepage
            return $this->redirectToRoute('home');
        }

    }
}
