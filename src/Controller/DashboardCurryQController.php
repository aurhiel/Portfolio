<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// Entities
use App\Entity\CurryQEducation;
use App\Entity\CurryQCareer;
use App\Entity\CurryQSkill;
use App\Entity\CurryQSkillCategory;
use App\Entity\CurryQProject;

// Forms
use App\Form\CurryQEducationType;
use App\Form\CurryQCareerType;
use App\Form\CurryQSkillType;
use App\Form\CurryQSkillCategoryType;
use App\Form\CurryQProjectType;

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

        // 1) Build the form
        $cv_edu = new CurryQEducation();
        $form = $this->createForm(CurryQEducationType::class, $cv_edu, [
            'action' => $this->generateUrl('dashboard_curry_q_education')
        ]);
        // 2) Handle form
        $form->handleRequest($request);
        $return = array();
        // 3) Save !
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($cv_edu);

            // 4) Try to save (flush) or clear
            try {
                // Flush OK !
                $em->flush();

                $return = array(
                    'query_status'    => 1,
                    'message_status'  => 'Sauvegarde effectuée avec succès.',
                    'id_entity'       => $cv_edu->getId()
                );

                // Clear/reset form
                $cv_edu = new CurryQEducation();
                $form = $this->createForm(CurryQEducationType::class, $cv_edu);
            } catch (\Exception $e) {
                // Something goes wrong
                $em->clear();

                $return = array(
                    'query_status'    => 0,
                    'exception'       => $e->getMessage(),
                    'message_status'  => 'Un problème est survenu lors de la sauvegarde de la formation.'
                );
            }

            // 5) Set flash message
            $request->getSession()->getFlashBag()->add(
                (($return['query_status'] == 1) ? 'success' : 'error'),
                $return['message_status']
            );
        }

        // Retrieve educations
        $r_edu      = $em->getRepository(CurryQEducation::class);
        $educations = $r_edu->findAll();

        return $this->render('dashboard/curry-q/education.html.twig', [
            'form'        => $form->createView(),
            'educations'  => $educations
        ]);
    }

    /**
     * @Route("/dashboard/curriculum-vitae/formations/delete/{id}", name="dashboard_curry_q_education_delete")
     */
    public function curry_q_education_delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve item to delete
        $repo   = $em->getRepository(CurryQEducation::class);
        $entity = $repo->findOneById($id);

        if ($entity !== null) {
            $em->remove($entity);
            $em->flush();
        } else {
            $request->getSession()->getFlashBag()->add('error',
              'La formation avec pour ID: <b>' . $id . '</b> n\'existe pas en base de données.');
        }

        // No direct access
        return $this->redirectToRoute('dashboard_curry_q_education');
    }

    /**
     * @Route("/dashboard/curriculum-vitae/parcours", name="dashboard_curry_q_career")
     */
    public function curry_q_career(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // 1) Build the form
        $entity = new CurryQCareer();
        $form = $this->createForm(CurryQCareerType::class, $entity, [
            'action' => $this->generateUrl('dashboard_curry_q_career')
        ]);
        // 2) Handle form
        $form->handleRequest($request);
        $return = array();
        // 3) Save !
        if ($form->isSubmitted() && $form->isValid()) {
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
                $entity = new CurryQCareer();
                $form = $this->createForm(CurryQCareerType::class, $entity);
            } catch (\Exception $e) {
                // Something goes wrong
                $em->clear();

                $return = array(
                    'query_status'    => 0,
                    'exception'       => $e->getMessage(),
                    'message_status'  => 'Un problème est survenu lors de la sauvegarde du parcours.'
                );
            }

            // 5) Set flash message
            $request->getSession()->getFlashBag()->add(
                (($return['query_status'] == 1) ? 'success' : 'error'),
                $return['message_status']
            );
        }

        // Retrieve skills & categories
        $r_careers = $em->getRepository(CurryQCareer::class);
        $careers   = $r_careers->findAll();

        return $this->render('dashboard/curry-q/career.html.twig', [
            'form'    => $form->createView(),
            'careers' => $careers
        ]);
    }

    /**
     * @Route("/dashboard/curriculum-vitae/parcours/delete/{id}", name="dashboard_curry_q_career_delete")
     */
    public function curry_q_career_delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve item to delete
        $repo   = $em->getRepository(CurryQCareer::class);
        $entity = $repo->findOneById($id);

        if ($entity !== null) {
            $em->remove($entity);
            $em->flush();
        } else {
            $request->getSession()->getFlashBag()->add('error',
              'Le parcours avec pour ID: <b>' . $id . '</b> n\'existe pas en base de données.');
        }

        // No direct access
        return $this->redirectToRoute('dashboard_curry_q_career');
    }

    /**
     * @Route("/dashboard/curriculum-vitae/competences", name="dashboard_curry_q_skills")
     */
    public function curry_q_skills(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // 1) Build the skill's form
        $cv_skill = new CurryQSkill();
        $form_skill = $this->createForm(CurryQSkillType::class, $cv_skill, [
            'action' => $this->generateUrl('dashboard_curry_q_skills')
        ]);
        // 2) Handle form
        $form_skill->handleRequest($request);
        $return = array();
        // 3) Save !
        if ($form_skill->isSubmitted() && $form_skill->isValid()) {
            $em->persist($cv_skill);

            // 4) Try to save (flush) or clear
            try {
                // Flush OK !
                $em->flush();

                $return = array(
                    'query_status'    => 1,
                    'message_status'  => 'Sauvegarde effectuée avec succès.',
                    'id_entity'       => $cv_skill->getId()
                );

                // Clear/reset form
                $cv_skill = new CurryQSkill();
                $form_skill = $this->createForm(CurryQSkillType::class, $cv_skill);
            } catch (\Exception $e) {
                // Something goes wrong
                $em->clear();

                $return = array(
                    'query_status'    => 0,
                    'exception'       => $e->getMessage(),
                    'message_status'  => 'Un problème est survenu lors de la sauvegarde de la compétence.'
                );
            }

            // 5) Set flash message
            $request->getSession()->getFlashBag()->add(
                (($return['query_status'] == 1) ? 'success' : 'error'),
                $return['message_status']
            );
        }

        // 1.1) Build the categories skill's form
        $cv_skill_category = new CurryQSkillCategory();
        $form_category = $this->createForm(CurryQSkillCategoryType::class, $cv_skill_category, [
            'action' => $this->generateUrl('dashboard_curry_q_skills')
        ]);
        // 2.1) Handle form
        $form_category->handleRequest($request);
        $return = array();
        // 3.1) Save !
        if ($form_category->isSubmitted() && $form_category->isValid()) {
            $em->persist($cv_skill_category);

            // 4.1) Try to save (flush) or clear
            try {
                // Flush OK !
                $em->flush();

                $return = array(
                    'query_status'    => 1,
                    'message_status'  => 'Sauvegarde effectuée avec succès.',
                    'id_entity'       => $cv_skill_category->getId()
                );

                // Clear/reset form
                $cv_skill_category = new CurryQSkillCategory();
                $form_category = $this->createForm(CurryQSkillCategoryType::class, $cv_skill_category);
            } catch (\Exception $e) {
                // Something goes wrong
                $em->clear();

                $return = array(
                    'query_status'    => 0,
                    'exception'       => $e->getMessage(),
                    'message_status'  => 'Un problème est survenu lors de la sauvegarde de la catégorie.'
                );
            }

            // 5.1) Set flash message
            $request->getSession()->getFlashBag()->add(
                (($return['query_status'] == 1) ? 'success' : 'error'),
                $return['message_status']
            );
        }

        // Retrieve skills & categories
        $r_skills = $em->getRepository(CurryQSkill::class);
        $skills   = $r_skills->findAll();

        $r_skills_categories = $em->getRepository(CurryQSkillCategory::class);
        $skills_categories   = $r_skills_categories->findAll();

        return $this->render('dashboard/curry-q/skills.html.twig', [
            'form_skill'          => $form_skill->createView(),
            'form_skill_category' => $form_category->createView(),
            'skills'              => $skills,
            'skills_categories'   => $skills_categories
        ]);
    }

    /**
     * @Route("/dashboard/curriculum-vitae/competences/delete/{id}", name="dashboard_curry_q_skills_delete")
     */
    public function curry_q_skills_delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve item to delete
        $repo   = $em->getRepository(CurryQSkill::class);
        $entity = $repo->findOneById($id);

        if ($entity !== null) {
            $em->remove($entity);
            $em->flush();
        } else {
            $request->getSession()->getFlashBag()->add('error',
              'La compétence avec pour ID: <b>' . $id . '</b> n\'existe pas en base de données.');
        }

        // No direct access
        return $this->redirectToRoute('dashboard_curry_q_skills');
    }

    /**
     * @Route("/dashboard/curriculum-vitae/competences/categorie/delete/{id}", name="dashboard_curry_q_skills_categories_delete")
     */
    public function curry_q_skills_categories_delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve item to delete
        $repo   = $em->getRepository(CurryQSkillCategory::class);
        $entity = $repo->findOneById($id);

        if ($entity !== null) {
            $em->remove($entity);
            $em->flush();
        } else {
            $request->getSession()->getFlashBag()->add('error',
              'La catégorie de compétence avec pour ID: <b>' . $id . '</b> n\'existe pas en base de données.');
        }

        // No direct access
        return $this->redirectToRoute('dashboard_curry_q_skills');
    }

    /**
     * @Route("/dashboard/curriculum-vitae/projets", name="dashboard_curry_q_projects")
     */
    public function curry_q_projects(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // 1) Build the form
        $entity = new CurryQProject();
        $form = $this->createForm(CurryQProjectType::class, $entity, [
            'action' => $this->generateUrl('dashboard_curry_q_projects')
        ]);
        // 2) Handle form
        $form->handleRequest($request);
        $return = array();
        // 3) Save !
        if ($form->isSubmitted() && $form->isValid()) {
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
                $entity = new CurryQProject();
                $form = $this->createForm(CurryQProjectType::class, $entity);
            } catch (\Exception $e) {
                // Something goes wrong
                $em->clear();

                $return = array(
                    'query_status'    => 0,
                    'exception'       => $e->getMessage(),
                    'message_status'  => 'Un problème est survenu lors de la sauvegarde du projet.'
                );
            }

            // 5) Set flash message
            $request->getSession()->getFlashBag()->add(
                (($return['query_status'] == 1) ? 'success' : 'error'),
                $return['message_status']
            );
        }

        // Retrieve projects
        $r_projects = $em->getRepository(CurryQProject::class);
        $projects   = $r_projects->findAll();

        return $this->render('dashboard/curry-q/projects.html.twig', [
            'form'      => $form->createView(),
            'projects'  => $projects
        ]);
    }

    /**
     * @Route("/dashboard/curriculum-vitae/projets/delete/{id}", name="dashboard_curry_q_projects_delete")
     */
    public function curry_q_projects_delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Retrieve item to delete
        $repo   = $em->getRepository(CurryQProject::class);
        $entity = $repo->findOneById($id);

        if ($entity !== null) {
            $em->remove($entity);
            $em->flush();
        } else {
            $request->getSession()->getFlashBag()->add('error',
              'Le project avec pour ID: <b>' . $id . '</b> n\'existe pas en base de données.');
        }

        // No direct access
        return $this->redirectToRoute('dashboard_curry_q_projects');
    }
}
