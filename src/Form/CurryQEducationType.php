<?php

namespace App\Form;

use App\Entity\CurryQEducation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CurryQEducationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label'       => 'form_education.name.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_education.name.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('year_start', IntegerType::class, [
                'label'       => 'form_education.year_start.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_education.year_start.placeholder',
                    'class'       => 'required-giga',
                    'min'         => 2005,
                    'max'         => 2080
                ]
            ])
            ->add('year_end', IntegerType::class, [
                'label'       => 'form_education.year_end.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_education.year_end.placeholder',
                    'class'       => 'required-giga',
                    'min'         => 2005,
                    'max'         => 2080
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
            'data_class' => CurryQEducation::class,
        ]);
    }
}
