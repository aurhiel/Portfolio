<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// Entities
use App\Entity\CurryQSkill;
use App\Entity\CurryQSkillCategory;

// Forms
use App\Form\CurryQSkillType;
use App\Form\CurryQSkillCategoryType;

/**
  * Require ROLE_ADMIN for *every* controller method in this class.
  *
  * @IsGranted("ROLE_ADMIN")
  */
class DashboardCurryQController extends AbstractController
{
    /**
     * @Route("/dashboard/curriculum-vitae/formations", name="dashboard_curry_q_education")
     */
    public function curry_q_education(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        return $this->render('dashboard/curry-q/education.html.twig');
    }

    /**
     * @Route("/dashboard/curriculum-vitae/parcours", name="dashboard_curry_q_career")
     */
    public function curry_q_career(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        return $this->render('dashboard/curry-q/career.html.twig');
    }

    /**
     * @Route("/dashboard/curriculum-vitae/competences", name="dashboard_curry_q_skills")
     */
    public function curry_q_skills(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // 1) Build the form
        $cv_skill = new CurryQSkill();
        $form = $this->createForm(CurryQSkillType::class, $cv_skill, [
            'action' => $this->generateUrl('dashboard_curry_q_skills')
        ]);

        // 2) Handle form
        $form->handleRequest($request);
        $data = array();

        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Save !
            $em->persist($cv_skill);
        }
        dump('toto');

        return $this->render('dashboard/curry-q/skills.html.twig');
    }

    /**
     * @Route("/dashboard/curriculum-vitae/projets", name="dashboard_curry_q_projects")
     */
    public function curry_q_(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        return $this->render('dashboard/curry-q/projects.html.twig');
    }
}
