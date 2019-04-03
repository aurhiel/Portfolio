<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname',       TextType::class, [
                'label'       => 'form_contact.lastname.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_contact.lastname.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('firstname',      TextType::class, [
                'label'       => 'form_contact.firstname.label',
                'required'    => true,
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_contact.firstname.placeholder',
                    'class'       => 'required-supra'
                ]
            ])
            ->add('email',          EmailType::class, [
                'label'       => 'form_contact.email.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_contact.email.placeholder',
                    'class'       => 'required-supra'
                ]
            ])
            // Anti-bot attempt
            ->add('email_confirm',  EmailType::class, [
                'label'       => false,
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_contact.email_confirm.placeholder',
                    'class'       => 'required-maxi',
                    // remove tab focus for classic users (but may help bot to not complete the input ...)
                    'tabIndex'    => '-1'
                ],
                'required'    => false
            ])
            ->add('message',        TextareaType::class, [
                'label'       => 'form_contact.message.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_contact.message.placeholder',
                    'rows'        => 5,
                    'class'       => 'required-giga'
                ]
            ])
            ->add('is_quote',       CheckboxType::class, [
                'label'       => 'form_contact.is_quote.label',
                'label_attr'  => [ 'class' => 'checkbox-custom' ],
                'required'    => false,
                'mapped'      => false
            ])
            ->add('amount',         ChoiceType::class, [
                'label'       => 'form_contact.amount.label',
                'label_attr'  => ['class' => 'sr-only'],
                // 'label_attr'  => ['class' => 'custom-select'], // TODO : not working ... (select-custom either)
                'placeholder' => 'form_contact.amount.placeholder',
                'required'    => false,
                'choices'     => [
                  'form_contact.amount.choices.1.label' => '.5-1k',
                  'form_contact.amount.choices.2.label' => '1-5k',
                  'form_contact.amount.choices.3.label' => '5-10k',
                  'form_contact.amount.choices.4.label' => '10k+'
                ]
            ])
            ->add('project_type',   ChoiceType::class, [
                'label'       => 'form_contact.project_type.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                // 'label_attr'  => ['class' => 'custom-select'], // TODO : not working ... (select-custom either)
                'placeholder' => 'form_contact.project_type.placeholder',
                'required'    => false,
                'choices'     => [
                  'form_contact.project_type.choices.1.label' => 'from-scratch',
                  'form_contact.project_type.choices.2.label' => 'cms',
                  'form_contact.project_type.choices.3.label' => 'landing',
                  'form_contact.project_type.choices.4.label' => 'update'
                ]
            ])
            ->add('cancel',         ButtonType::class, [
                'label' => 'form_contact.cancel.label',
                'attr'  => [
                    'class' => 'btn-toggle-form-contact btn-outline-secondary text-white mx-2'
                ]
            ])
            ->add('send',           SubmitType::class, [
                'label' => 'form_contact.submit.label',
                'attr'  => [
                    'class' => 'btn-send btn-primary px-5 mx-2'
                ]
            ])
        ;
    }
}
