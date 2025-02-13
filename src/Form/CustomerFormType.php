<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Offer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('offer', EntityType::class, [
                'class' => Offer::class,
                'label' => false,
                'choice_label' => 'label',
                'multiple' => true,
                'expanded' => true,
                'group_by' => function ($offer) {
                    return $offer->getCategory()->getLabel();
                },
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
