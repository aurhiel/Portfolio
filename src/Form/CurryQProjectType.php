<?php

namespace App\Form;

use App\Entity\CurryQProject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CurryQProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_start', DateType::class, array(
              'label'       => 'form_cv_project.date_start.label',
              'label_attr'  => [ 'class' => 'ml-2'],
              'widget'      => 'single_text'
            ))
            ->add('date_end', DateType::class, array(
              'label'       => 'form_cv_project.date_end.label',
              'label_attr'  => [ 'class' => 'ml-2'],
              'widget'      => 'single_text',
              'required'    => false
            ))
            ->add('label', TextType::class, [
                'label'       => 'form_cv_project.name.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_cv_project.name.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label'       => 'form_cv_project.description.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'required'    => false,
                'attr'        => [
                    'placeholder' => 'form_cv_project.description.placeholder',
                    'class'       => 'required-giga'
                ]
  					])
            ->add('send', SubmitType::class, [
                'label' => 'form_basic.save.label',
                'attr'  => [
                    'class' => 'btn-send btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CurryQProject::class,
        ]);
    }
}
