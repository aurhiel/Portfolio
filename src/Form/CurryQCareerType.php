<?php

namespace App\Form;

use App\Entity\CurryQCareer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurryQCareerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('job')
            ->add('description')
            ->add('company')
            ->add('date_start')
            ->add('date_end')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CurryQCareer::class,
        ]);
    }
}
