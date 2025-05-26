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
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
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
            ->add('nom', TextType::class, [
                'pattern' => "^[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}$",
                'attr' => [
                    'pattern' => "^[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}$",
                    'data-empty' => 'Merci de renseigner votre prénom.',
                    'data-format' => 'Le prénom ne doit contenir que des lettres, espaces, tirets ou apostrophes (2 à 50 caractères).',
                ],
                new Regex([
                    'pattern' => "/^[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}$/",
                    'match' => true,
                    'message' => 'Le nom/prénom ne doit contenir que des lettres, des espaces, tirets ou apostrophes (2 à 50 caractères).'
                ]),
                // Description
                'attr' => [
                    'pattern' => "^[A-Za-zÀ-ÖØ-öø-ÿ0-9.,;:!?()'\" \r\n\-]{2,255}$",
                    'title'   => "2 à 255 caractères : lettres, chiffres, ponctuation légère, pas de < ni >",
                    'data-format' => 'La description de votre activité doit contenir uniquement des lettres, chiffres, ponctuations simples (2 à 255 caractères).',

                ],
                new Regex([
                    'pattern' => "/^[A-Za-zÀ-ÖØ-öø-ÿ0-9\.,;:!\?\(\)'\" \r\n\-]{2,255}$/u",
                    'match'   => true,
                    'message' => "La description doit faire entre 2 et 255 caractères et ne peut contenir ni '<' ni '>'.",
                ]),
                // Siren
                'data-format' => 'Le numéro SIREN doit contenir exactement 9 chiffres (exemple : 732829320).',
                new Regex([
                    'pattern' => '/^[0-9]{9}$/',
                    'message' => 'Le numéro SIREN doit contenir exactement 9 chiffres (exemple : 732829320).',
                ]),
                // TEL
                'attr' => [
                    'pattern' => '^(06|07)(\s[0-9]{2}){4}$',
                    'data-empty' => 'Merci de renseigner votre numéro de téléphone.',
                    'data-format' => 'Le numéro de téléphone doit être au format 06 12 34 56 78.',
                ],




            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'required' => true,
                'attr' => [
                    'data-empty' => 'Merci de renseigner votre adresse email.',
                    'data-format' => 'Veuillez entrer une adresse email valide (exemple : nom@domaine.fr).',
                    // Optionnel : si tu veux forcer un pattern simple
                    'pattern' => '^[^@\s]+@[^@\s]+\.[^@\s]+$',
                ],
                'help' => 'Exemple : nom@domaine.fr',
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse email est obligatoire.']),
                    new Email([
                        'mode' => 'strict', // Optionnel : stricte RFC validation
                        'message' => 'Veuillez entrer une adresse email valide (exemple : nom@domaine.fr).',
                    ]),
                ],
            ])

            ->add('createdAt', TextType::class, [
                'label' => 'Date de création',
                'required' => true,
                'attr' => [
                    'pattern' => '^(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[0-2])/[0-9]{4}$',
                    'data-empty' => 'Merci de renseigner la date de création.',
                    'data-format' => 'La date doit être au format JJ/MM/YYYY (exemple : 06/12/2000).',
                    // OU
                    'data-format' => 'La date doit être au format jour/mois/année (exemple : 06/12/2000).',

                ],
                'help' => 'Exemple : 06/12/2000',
                'constraints' => [
                    new NotBlank(['message' => 'La date de création est obligatoire.']),
                    new Regex([
                        'pattern' => '/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/[0-9]{4}$/',
                        'message' => 'La date doit être au format JJ/MM/YYYY (exemple : 06/12/2000).',
                    ]),
                ],
            ])

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
                'choice_label' => fn(Action $action) => $action->getNameAction(),  // Utiliser la fonction pour récupérer un nom personnalisé
                'multiple' => false,  // Permet de ne sélectionner qu'une seule action
                'expanded' => true,   // Affiche sous forme de boutons radio
                'choice_attr' => function ($action) {
                    return [
                        'class' => 'btn-check mandatory',  // Ajouter des classes ou d'autres attributs
                        'data-id' => $action->getId()  // Exemple d'attribut personnalisé
                    ];
                },
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
