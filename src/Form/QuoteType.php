<?php

namespace App\Form;

use App\Entity\Quote;
use App\Entity\Client;

use App\Repository\ClientRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', EntityType::class, [
                'class'         => Client::class,
                'label'         => 'form_quote.client.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'required'      => true,
                'placeholder'   => 'form_quote.client.placeholder',
                'query_builder' => function (ClientRepository $repo) {
                    return $repo->createQueryBuilder('c')
                        // Order on client lastname & firstname
                        ->addOrderBy('c.lastname', 'ASC')
                        ->addOrderBy('c.firstname', 'ASC');
                },
                'choice_label'  => function ($client) {
                    return $client->getLastname() . ' ' . $client->getFirstname() . ' (' . $client->getCompany() . ')';
                }
            ])
            ->add('label', TextType::class, [
                'label'       => 'form_quote.name.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_quote.name.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('sku', TextType::class, [
                'label'       => 'form_quote.sku.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'required'    => false,
                'attr'        => [
                    'placeholder' => 'form_quote.sku.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('document', FileType::class, [
                'label' => 'form_quote.document.label',

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
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'form_quote.document.error', // = Please upload a valid PDF document
                    ])
                ],
            ])
            ->add('date_created', DateType::class, [
                'label'       => 'form_quote.date_created.label',
                'label_attr'  => [ 'class' => 'ml-2'],
                'widget'      => 'single_text',
                'required'    => false
            ])
            ->add('date_signed', DateType::class, [
                'label'       => 'form_quote.date_signed.label',
                'label_attr'  => [ 'class' => 'ml-2'],
                'widget'      => 'single_text',
                'required'    => false
            ])
            ->add('amount', MoneyType::class, [
                'label'       => 'form_quote.amount.label'
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
            'data_class' => Quote::class,
        ]);
    }
}
