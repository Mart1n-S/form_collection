<?php

namespace App\Form;

use App\Entity\Membre;
use App\Entity\Statut;
use Symfony\Component\Form\AbstractType;
use PHPUnit\TextUI\XmlConfiguration\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class MembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class, [
                // 'constraints' => [
                //     new Assert\Length([
                //         'max' => 5,
                //         'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.',
                //     ]),
                // ],
            ])
            ->add('email', EmailType::class)
            ->add('situationMaritale', TextType::class)
            ->add('statut', EntityType::class, [
                'class' => Statut::class,
                'choice_label' => 'nom', // Affiche le nom du statut dans le select
                'placeholder' => 'Sélectionnez un statut',
                'required' => true,
                // Gérer message d'erreur pour un champ de type EntityType
                'invalid_message' => 'Le statut sélectionné n\'est pas valide.',
            ])
            ->add('cni', FileType::class, [
                'mapped' => true,
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '1M',
                        'mimeTypes' => [
                            'application/pdf',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un document PDF valide.',
                        'maxSizeMessage' => 'Le fichier est trop volumineux. La taille maximale autorisée est de {{ limit }} {{ suffix }}.',
                    ]),
                ],
            ])
            ->add('justificatifDomicile', FileType::class, [
                'mapped' => true,
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '1M',
                        'mimeTypes' => [
                            'application/pdf',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un document PDF valide.',
                        'maxSizeMessage' => 'Le fichier est trop volumineux. La taille maximale autorisée est de {{ limit }} {{ suffix }}.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Membre::class,
        ]);
    }
}
