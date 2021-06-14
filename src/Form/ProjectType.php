<?php

namespace App\Form;

// Entities
use App\Entity\Project;
use App\Entity\ProjectSpec;

// Repositories
use App\Repository\ProjectRepository;
use App\Repository\ProjectSpecRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'       => 'form_project.name.label',
                'required'    => false,
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_project.name.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('name_long', TextType::class, [
                'label'       => 'form_project.name_long.label',
                'required'    => false,
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_project.name_long.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('url', TextType::class, [
                'label'       => 'form_project.url.label',
                'required'    => false,
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_project.url.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label'       => 'form_project.description.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_project.description.placeholder',
                    // 'class' => 'required-giga'
                ]
  					])
            ->add('illustration', FileType::class, [
                'label' => 'form_project.illustration.label',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'form_project.illustration.error', // = Please upload a valid image
                    ])
                ],
            ])
            ->add('specs', EntityType::class, [
                'class'         => ProjectSpec::class,
                'label'         => 'form_project.specs.label',
                'label_attr'    => [ 'class' => 'checkbox-custom' ],
                'query_builder' => function (ProjectSpecRepository $repo) {
                    // Re-order projects by their name
                    return $repo->createQueryBuilder('ps')
                        ->addOrderBy('ps.name', 'ASC');
                },
                'choice_label'  => function ($project_spec) {
                    return $project_spec->getName();
                },
                'multiple'  => true,
                'expanded'  => true,
                // 'attr'      => [
                //   'class' => 'custom-select'
                // ],
            ])
            ->add('screenshots', FileType::class,[
                'label'       => 'form_project.screenshots.label',
                'label_attr'  => [ 'class' => 'col-form-label fw-bold'],
                'attr'        => [ 'class' => 'form-control' ],
                'multiple'    => true,
                'mapped'      => false,
                'required'    => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                // 'constraints' => [
                //     new File([
                //         'maxSize' => '4096k',
                //         'mimeTypes' => [ 'image/*' ],
                //         'mimeTypesMessage' => 'Please upload a valid Image file',
                //     ])
                // ],
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
            'data_class' => Project::class,
        ]);
    }
}
