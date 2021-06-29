<?php

namespace App\Form;

use App\Entity\Expense;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ExpenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'       => 'form_expense.name.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_expense.name.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('amount', MoneyType::class, [
                'label'       => 'form_expense.amount.label',
                'label_attr'  => [ 'class' => 'sr-only'],
                'attr'        => [
                    'placeholder' => 'form_expense.amount.placeholder',
                    'class'       => 'required-giga'
                ]
            ])
            ->add('periodType', ChoiceType::class, [
                'label'         => 'form_expense.period_type.label',
                'label_attr'    => [ 'class' => 'sr-only'],
                'choices'       => Expense::PERIOD_TYPES,
                'choice_label'  => function($period_type) {
                    return 'form_expense.' . $period_type . '.label';
                },
                // 'attr'        => [
                //     'placeholder' => 'form_expense.period_type.placeholder',
                //     'class'       => 'required-giga'
                // ]
            ])
            ->add('send', SubmitType::class, [
                'label' => 'form_basic.save.label',
                'attr'  => [
                    'class' => 'btn-send btn-primary px-5'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Expense::class,
        ]);
    }
}
