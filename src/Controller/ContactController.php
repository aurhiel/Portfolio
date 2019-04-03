<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// Entities
use App\Entity\Contact;

// Forms
use App\Form\ContactType;


class ContactController extends AbstractController
{
    /**
     * @Route("/contact.html", name="contact")
     */
    public function index(Request $request, \Swift_Mailer $mailer)
    {
        $contact      = new Contact();
        $form_contact = $this->createForm(ContactType::class, $contact, [
            'action' => $this->generateUrl('contact')
        ]);

        // Handle form
        $form_contact->handleRequest($request);

        // Set default error message
        $status         = 0;
        $message_status = "Un problème est survenu lors de l'envoi du mail, veuillez essayer de nouveau ou me contacter via l'adresse : litti.aurelien@gmail.com.";

        if ($form_contact->isSubmitted() && $form_contact->isValid()) {
            $contact  = $form_contact->getData();
            $is_quote = !empty($contact->getAmount()) || !empty($contact->getProjectType());

            // Set message title
            $message_title = (($is_quote == true) ? "Demande de devis" : "Message classique");

            // Push lastname and firstname to message subject/title
            $message_title .= " de " . $contact->getLastname() . " " . $contact->getFirstname();

            // Try sending email
            try {
                // Anti-bot attempt (email confirm is a fake input)
                if (empty($contact->getEmailConfirm())) {
                    $message = (new \Swift_Message('[litti-aurelien.fr] ' . $message_title))
                        ->setFrom([
                            $contact->getEmail() => $contact->getFirstname() . ' ' . $contact->getLastname()
                        ])
                        ->setTo('litti.aurelien@gmail.com')
                        ->setBody(
                            $this->renderView(
                                'emails/message.html.twig',
                                [
                                    'contact' => $contact,
                                    'visitor' => [
                                          'ip'    => $request->getClientIp(),
                                          'agent' => $request->server->get('HTTP_USER_AGENT')
                                    ],
                                ]
                            ),
                            'text/html'
                        )
                    ;

                    // Send email
                    $mailer->send($message);
                }

                $status         = 1;
                $message_status = "Votre " . (($is_quote == true) ? "devis" : "message" ) . " a bien été envoyé, je vous répondrai dès sa réception, à très bientôt !";
            } catch (\Exception $e) {
                // $request->getSession()->getFlashBag()->add('error',
                //   "");
            }
        } else {
            // TODO Catch form errors for JS ?
            // dump('meh.');
            $message_status = "Le formulaire n'a pas été rempli correctement ! Veuillez essayer de nouveau ou bien me contacter via l'adresse : litti.aurelien@gmail.com";
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'status'  => $status,
                'message' => $message_status
            ]);
        } else {
            // No JS = Contact page
            $request->getSession()->getFlashBag()->add(
                (($status == 1) ? 'success' : 'error'), $message_status
            );

            return $this->render('contact.html.twig', [
                'core_class'    => 'app-core--contact',
                'form_contact'  => $form_contact->createView()
            ]);
        }

    }
}
