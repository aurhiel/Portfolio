<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company',    TextType::class, [
                'label'       => 'form_client.company.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_client.company.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('lastname',   TextType::class, [
                'label'       => 'form_client.lastname.label',
                'required'    => false,
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_client.lastname.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('firstname',  TextType::class, [
                'label'       => 'form_client.firstname.label',
                'required'    => false,
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_client.firstname.placeholder',
                    'class'       => 'required-supra'
                ]
            ])
            ->add('email',      EmailType::class, [
                'label'       => 'form_client.email.label',
                'required'    => false,
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_client.email.placeholder',
                    'class'       => 'required-supra'
                ]
            ])
            ->add('color',      ColorType::class, [
                'label'       => 'form_client.color.label',
                'required'    => false,
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_client.color.placeholder',
                    'class'       => 'required-supra'
                ]
            ])
            ->add('send',       SubmitType::class, [
                'label' => 'form_contact.submit.label',
                'attr'  => [
                    'class' => 'btn-send btn-primary px-5'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
