<?php

namespace App\Form;

use App\Entity\Action;
use App\Form\MembreType;
use App\Entity\Association;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\ActionRepository;
use Symfony\Component\Form\AbstractType;
use PhpParser\Node\Scalar\MagicConst\Dir;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AssociationType extends AbstractType
{
    private $actionRepository;

    public function __construct(ActionRepository $actionRepository)
    {
        $this->actionRepository = $actionRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('dateCreation', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => true,
                'required' => true,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\NotBlank([
                        'message' => 'La date de création est obligatoire.',
                    ]),
                ],
            ])
            // Option 1
            // ->add('action', EntityType::class, [
            //     'class' => Action::class,
            //     'choice_label' => 'nameAction',
            //     'multiple' => false, // Permet de ne sélectionner qu'une seule action
            //     'expanded' => true, // Affiche les options sous forme de boutons radio
            // ])
            // Option2
            ->add('action', EntityType::class, [
                'class' => Action::class,
                'choice_label' => fn(Action $action) => $action->getNameAction(), // Utilise le nom comme étiquette
                'multiple' => true,  // Permet de sélectionner plusieurs actions
                'expanded' => true,   // Affiche sous forme de cases à cocher
                'choice_value' => fn(?Action $action) => $action ? $action->getIndex() : '',
                'choice_attr' => fn(Action $action) => [
                    'data-id' => $action->getId(),
                ],
            ])

            ->add('email', EmailType::class)
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
            ])
            ->add('membres', CollectionType::class, [
                'entry_type' => MembreType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'mapped' => true,
                'by_reference' => false,
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'captcha',
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
        ]);
    }
}
