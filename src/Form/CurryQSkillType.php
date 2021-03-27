<?php

namespace App\Form;

use App\Entity\CurryQSkill;
use App\Entity\CurryQSkillCategory;

use App\Repository\CurryQSkillCategoryRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CurryQSkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label'       => 'form_skill.name.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_skill.name.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('level', IntegerType::class, [
                'label'       => 'form_skill.level.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_skill.level.placeholder',
                    'class'       => 'required-giga',
                    'min'         => 1,
                    'max'         => 5
                ]
            ])
            ->add('category', EntityType::class, [
              'class'         => CurryQSkillCategory::class,
              'label'         => 'form_skill.category.label',
              'label_attr'  => [ 'class' => 'sr-only'],
              'required'      => true,
              'placeholder'   => 'form_skill.category.placeholder',
              'query_builder' => function (CurryQSkillCategoryRepository $repo) {
                  return $repo->createQueryBuilder('c')
                      // Order on category label
                      ->addOrderBy('c.position', 'ASC');
              },
              'choice_label'  => function ($skill_category) {
                  return $skill_category->getLabel();
              },
              'attr' => [
                'class' => 'custom-select'
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
            'data_class' => CurryQSkill::class,
        ]);
    }
}
