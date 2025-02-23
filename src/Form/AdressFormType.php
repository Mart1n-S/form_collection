<?php

namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdressFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse', TextType::class, [
                "attr" => [
                    "class" => "form-control",
                    "required" => true,
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'L\'adresse ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('ville', TextType::class, [
                "attr" => [
                    "class" => "form-control",
                    "required" => true,
                ],
            ])
            ->add('codePostal', TextType::class, [
                "attr" => [
                    "class" => "form-control",
                    "required" => true,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
        ]);
    }
}
