<?php

namespace App\Form;

use App\Entity\CurryQSkillCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CurryQSkillCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label'       => 'form_skill_category.name.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_skill_category.name.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('position', IntegerType::class, [
                'label'       => 'form_skill_category.position.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_skill_category.position.placeholder',
                    'class'       => 'required-giga',
                    'min'         => 1,
                    'max'         => 100
                ]
            ])
            ->add('slug', TextType::class, [
                'label'       => 'form_skill_category.slug.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_skill_category.slug.placeholder',
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
            'data_class' => CurryQSkillCategory::class,
        ]);
    }
}
