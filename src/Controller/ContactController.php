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
    public function index(Request $request)
    {
        $contact      = new Contact();
        $form_contact = $this->createForm(ContactType::class, $contact, [
            'action' => $this->generateUrl('contact')
        ]);

        // Handle form
        $form_contact->handleRequest($request);

        if ($form_contact->isSubmitted() && $form_contact->isValid()) {
            $contact = $form_contact->getData();

            dump('yay !');
            dump($contact);
            dump($form_contact->isSubmitted());
            dump($form_contact->isValid());


            // return $this->redirectToRoute('task_success');
        } else {
            dump('meh.');
        }

        return $this->render('contact.html.twig', [
            'core_class'    => 'app-core--display-form-contact',
            'form_contact'  => $form_contact->createView()
        ]);
    }
}
