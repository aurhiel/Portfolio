<?php

namespace App\DataFixtures;

// Entities
use App\Entity\Client;
use App\Entity\Testimonial;

// Misc.
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $clients = [
            [
                'company'   => 'Ingeneria',
                'firtname'  => 'Dominique',
                'lastname'  => 'Eloïse',
                'email'     => 'eloise.dominique@ingeneria.fr',
            ],
            [
                'company'   => 'Carteland',
                'firtname'  => 'Gilles',
                'lastname'  => 'Combes',
                'email'     => 'gilles.combes@carteland.fr',
                'testimonial' => "Aurélien s'est rapidement intégré à l'équipe. Très à l'aise en front, il a également pris part aux développements back-end dans un environnement Prestashop. Ce fut une très bonne collaboration.",
                'sign-type'   => 'both'
            ],
            [
                'company'   => 'Styléo',
                'firtname'  => 'Tom',
                'lastname'  => 'Baroni',
                'email'     => 'tom.baroni@styleo.fr',
                'testimonial' => "Aurélien nous a donné un coup de pouce sur quelques projets lorsque nous étions en période de rush et son aide nous a été grandement bénéfique.
                  Il a su s'adapter rapidement à nos méthodes et pratiques de développement avec une bonne compréhension globale des projets.
                  Les délais annoncés ont été tenus, pour un travail très propre et raisonnablement facturé.
                  Nous en sommes fortement satisfaits et n'hésiterons pas à faire de nouveau appel à lui si l'occasion se présente.
                  Merci.",
                'sign-type' => 'names'
            ],
        ];

        foreach ($clients as $data_c) {
            $client = new Client();

            $client->setCompany($data_c['company']);
            $client->setFirstname($data_c['firtname']);
            $client->setLastname($data_c['lastname']);
            $client->setEmail($data_c['email']);

            if (isset($data_c['testimonial'])) {
                $testimonial = new Testimonial();

                $testimonial->setIp('127.0.0.1');
                $testimonial->setAgent('localhost');
                $testimonial->setMessage($data_c['testimonial']);
                $testimonial->setSignType($data_c['sign-type']);
                $testimonial->setIsActive(true);

                // Persist client's testimonial in manager
                $manager->persist($testimonial);

                $client->setTestimonial($testimonial);
            }

            // Persist client in manager
            $manager->persist($client);
        }

        // Save clients entities
        $manager->flush();
    }
}
