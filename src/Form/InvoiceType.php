<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\Quote;

use App\Repository\QuoteRepository;

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

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quote', EntityType::class, [
                'class'         => Quote::class,
                'label'         => 'form_invoice.quote.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'required'      => false,
                'placeholder'   => 'form_invoice.quote.placeholder',
                'query_builder' => function (QuoteRepository $repo) use ($options) {
                    $qb = $repo->createQueryBuilder('q');

                    // Where $year
                    if (!is_null($options['year'])) {
                        $qb->where('YEAR(q.date_signed) = :year')
                          ->setParameter('year', (int)$options['year']);

                        $qb->orWhere('YEAR(q.date_signed) = :year_previous')
                          ->setParameter('year_previous', ((int)$options['year']) - 1);
                    }

                    // Order on date created, from newest to oldest
                    $qb->addOrderBy('q.date_signed', 'DESC');

                    return $qb;
                },
                'choice_label'  => function ($quote) {
                    return '[' . $quote->getDateSigned()->format('Y-m-d') . '] ' .((!empty($quote->getSku())) ? $quote->getSku() . ' - ' : '') . $quote->getLabel();
                },
                'attr' => [
                  'class' => 'custom-select'
                ]
            ])
            ->add('document', FileType::class, [
                'label' => 'form_invoice.document.label',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->add('sku', TextType::class, [
                'label'       => 'form_invoice.sku.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_invoice.sku.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('amount', MoneyType::class, [
                'label'       => 'form_invoice.amount.label'
            ])
            ->add('date_paid', DateType::class, [
                'label'       => 'form_invoice.date_paid.label',
                'label_attr'  => [ 'class' => 'ml-2'],
                'widget'      => 'single_text',
                'required'    => false
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
            'data_class'  => Invoice::class,
            'year'        => null
        ]);
    }
}
