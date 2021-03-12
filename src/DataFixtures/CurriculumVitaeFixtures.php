<?php

namespace App\DataFixtures;

// Entities
use App\Entity\CurryQEducation;
use App\Entity\CurryQCareer;
use App\Entity\CurryQSkill;
use App\Entity\CurryQSkillCategory;
use App\Entity\CurryQProject;

// Misc.
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CurriculumVitaeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //
        $educations = [
            [
                'label'       => 'Bac STI Génie Électronique',
                'year_start'  => 2007,
                'year_end'    => 2009,
            ],
            [
                'label'       => 'BTS Informatique & Réseau (IRIS)',
                'year_start'  => 2009,
                'year_end'    => 2011,
            ],
            [
                'label'       => 'Licence Pro<br> Multimédia Internet et Webmaster',
                'year_start'  => 2011,
                'year_end'    => 2012,
            ],
        ];

        foreach ($educations as $data_edu) {
            $edu = new CurryQEducation();

            $edu->setLabel($data_edu['label']);
            $edu->setYearStart($data_edu['year_start']);
            $edu->setYearEnd($data_edu['year_end']);

            // Persist education in manager
            $manager->persist($edu);
        }


        //
        $skillsCategories = [
            [
                'label'     => 'Techs. & framework',
                'slug'      => 'techs',
                'position'  => 1,
                'skills'    => [
                    [ 'label' => 'Git', 'level' => 3 ],
                    [ 'label' => 'Docker', 'level' => 4 ],
                    [ 'label' => 'Prestashop', 'level' => 4 ],
                    [ 'label' => 'Wordpress', 'level' => 5 ],
                    [ 'label' => 'Symfony', 'level' => 3 ],
                ]
            ],
            [
                'label'     => 'Logiciels',
                'slug'      => 'software',
                'position'  => 5,
                'skills'    => [
                    [ 'label' => 'SmartGit', 'level' => 4 ],
                    [ 'label' => 'PHPStorm / Atom', 'level' => 4 ],
                    [ 'label' => 'Adobe Photoshop', 'level' => 4 ],
                    [ 'label' => 'Libre/Open Office', 'level' => 5 ],
                ]
            ],
            [
                'label'     => 'Langages de dev.',
                'slug'      => 'dev',
                'position'  => 10,
                'skills'    => [
                    [ 'label' => 'PHP', 'level' => 4 ],
                    [ 'label' => 'HTML / CSS', 'level' => 5 ],
                    [ 'label' => 'SASS & Less', 'level' => 4 ],
                    [ 'label' => 'Javascript', 'level' => 5 ],
                ]
            ],
            [
                'label'     => 'Langues',
                'slug'      => 'speak',
                'position'  => 15,
                'skills'    => [
                    [ 'label' => 'Anglais', 'level' => 4 ],
                    [ 'label' => 'Italien', 'level' => 2 ],
                ]
            ],
        ];

        foreach ($skillsCategories as $data_category) {
            $skill_cat = new CurryQSkillCategory();

            $skill_cat->setLabel($data_category['label']);
            $skill_cat->setSlug($data_category['slug']);
            $skill_cat->setPosition($data_category['position']);

            foreach ($data_category['skills'] as $data_skill) {
                $skill = new CurryQSkill();

                $skill->setLabel($data_skill['label']);
                $skill->setLevel($data_skill['level']);

                // Persist skill in manager
                $manager->persist($skill);

                $skill_cat->addCurryQSkill($skill);
            }

            // Persist skill category in manager
            $manager->persist($skill_cat);
        }


        //
        $projects = [
            [
                'label'       => 'Kargain',
                'desc'        => "Prototype HTML d'une application de vente de véhicules BtoB et BtoC (Silex, Bootstrap & jQuery)",
                'date_start'  => '2018-03-13',
                'date_end'    => '2018-04-01'
            ],
            [
                'label'       => 'Ateliers Ingeneria',
                'desc'        => "Site de gestion d'ateliers pour la société de formation Ingeneria (Symfony, Bootstrap, jQuery & FullCalendar)",
                'date_start'  => '2018-04-20',
                'date_end'    => null
            ],
            [
                'label'       => 'WinMedia',
                'desc'        => 'Maintenance & améliorations de la solution WinMam de WinMedia (ReactJS)',
                'date_start'  => '2019-07-01',
                'date_end'    => '2019-09-01'
            ],
            [
                'label'       => 'Carteland',
                'desc'        => 'Site web de vente en ligne de faire-part (Prestashop, Bootstrap & jQuery)',
                'date_start'  => '2019-06-01',
                'date_end'    => '2020-02-01'
            ],
        ];

        foreach ($projects as $data_project) {
            $project = new CurryQProject();

            $project->setLabel($data_project['label']);
            $project->setDescription($data_project['desc']);
            $project->setDateStart(new \DateTime($data_project['date_start']));

            if (!is_null($data_project['date_end']))
                $project->setDateEnd(new \DateTime($data_project['date_end']));

            // Persist project in manager
            $manager->persist($project);
        }


        //
        $careers = [
            [
                'company'     => 'Live<span style="color:red;">2</span>Times',
                'job'         => 'Stagiaire',
                'desc'        => 'Maintenance et développement de sites',
                'date_start'  => '2012-04-01',
                'date_end'    => '2012-06-01'
            ],
            [
                'company'     => '<span style="color: yellowgreen;">e-frogg</span>',
                'job'         => 'Développeur Web / Intégrateur',
                'desc'        => 'Intégration & Développement sur divers CMS & framework (framework interne, Prestashop, Wordpress, Drupal, ...)',
                'date_start'  => '2012-08-01',
                'date_end'    => '2017-01-01'
            ],
            [
                'company'     => 'Indépendant',
                'job'         => 'Développeur Web Fullstack',
                'desc'        => 'Développement de sites / applications web en Micro-entreprise («from scratch», Symfony, Wordpress, ...)',
                'date_start'  => '2018-03-12',
                'date_end'    => null
            ],
        ];

        foreach ($careers as $data_career) {
            $career = new CurryQCareer();

            $career->setJob($data_career['job']);
            $career->setDescription($data_career['desc']);
            $career->setCompany($data_career['company']);
            $career->setDateStart(new \DateTime($data_career['date_start']));

            if (!is_null($data_career['date_end']))
                $career->setDateEnd(new \DateTime($data_career['date_end']));

            // Persist career in manager
            $manager->persist($career);
        }


        // Save entities
        $manager->flush();
    }
}
