<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// Entities
use App\Entity\CurryQEducation;
use App\Entity\CurryQCareer;
use App\Entity\CurryQSkillCategory;
use App\Entity\CurryQProject;

class CurryQController extends AbstractController
{
    /**
     * @Route("/curriculum-vitae.html", name="curriculum")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve CV Skills
        $r_skills_categories = $em->getRepository(CurryQSkillCategory::class);
        $skills_categories   = $r_skills_categories->findAll();

        // Retrieve CV Careers (jobs)
        $r_career = $em->getRepository(CurryQCareer::class);
        $careers = $r_career->findAll();

        // Retrieve CV Educations
        $r_edu      = $em->getRepository(CurryQEducation::class);
        $educations = $r_edu->findAll();

        // Retrieve CV Projects
        $r_projects = $em->getRepository(CurryQProject::class);
        $projects   = $r_projects->findAll();

        return $this->render('curriculum-vitae.html.twig', [
            'educations'          => $educations,
            'careers'             => $careers,
            'skills_categories'   => $skills_categories,
            'projects'            => $projects
        ]);
    }
}
