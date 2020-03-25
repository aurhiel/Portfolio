<?php

namespace App\Form;

use App\Entity\CurryQCareer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CurryQCareerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_start', DateType::class, array(
                'label'       => 'form_career.date_start.label',
                'label_attr'  => [ 'class' => 'ml-2'],
                'widget'      => 'single_text'
            ))
            ->add('date_end', DateType::class, array(
                'label'       => 'form_career.date_end.label',
                'label_attr'  => [ 'class' => 'ml-2'],
                'widget'      => 'single_text',
                'required'    => false
            ))
            ->add('job', TextType::class, [
                'label'       => 'form_career.job.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_career.job.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('company', TextType::class, [
                'label'       => 'form_career.company.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_career.company.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label'       => 'form_career.description.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'required'    => false,
                'attr'        => [
                    'placeholder' => 'form_career.description.placeholder',
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
            'data_class' => CurryQCareer::class,
        ]);
    }
}
