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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $categories = $options['categories']; // On passe les catégories au formulaire

        // Ajouter les actions par catégorie sous forme de cases à cocher
        foreach ($categories as $categorie) {
            $actions = $this->actionRepository->findBy(['categorie' => $categorie]); // Récupérer les actions de la catégorie
            $actionChoices = [];
            $choiceAttributes = [];

            foreach ($actions as $action) {
                $actionChoices[$action->getNameAction()] = $action->getId();
                $choiceAttributes[$action->getId()] = [
                    'data-description' => 'TEESSSST',
                    'data-index' => $action->getIndex(),
                ];
            }

            $builder->add($categorie->getName(), ChoiceType::class, [
                'label' => $categorie->getName(),
                'required' => false,
                'mapped' => false, // On ne mappe pas directement ces actions à l'entité Association
                'expanded' => true, // Affichage sous forme de cases à cocher
                'multiple' => true, // Permet de sélectionner plusieurs actions
                'choices' => $actionChoices,
                'choice_attr' => function ($choice, $key, $value) use ($choiceAttributes) {
                    return $choiceAttributes[$value] ?? []; // Associe les attributs personnalisés
                },
                'attr' => ['class' => 'actions-category'],
            ]);
        }


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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
            'categories' => [],
        ]);
    }
}
