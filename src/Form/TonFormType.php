<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('preference', ChoiceType::class, [
                'label' => 'Choisissez une préférence',
                'choices' => [
                    'Oui' => 'option1',
                    'Non' => 'option2',
                ],
                'expanded' => true, // Boutons radio
                'multiple' => false,
                'required' => true,
                'mapped' => false,
                'data' => 'option1',
            ])
            // Champs ajoutés par défaut mais désactivés
            ->add('matricule', TextType::class, [
                'label' => 'Votre matricule',
                'required' => false, // Désactivé par défaut
                'mapped' => false, // Ce champ n'existe pas dans l'entité
                'attr' => [
                    'pattern' => '\d{6}',  // Un exemple de pattern pour un matricule de 6 chiffres
                    'title' => 'Le matricule doit être composé de 6 chiffres.',
                    'data-help' => 'le matricule', // Aide pour l'utilisateur
                ],
            ])
            ->add('numero', TextType::class, [
                'label' => 'Votre numéro',
                'required' => false, // Désactivé par défaut
                'mapped' => false, // Ce champ n'existe pas dans l'entité
            ]);

        // Ajouter un EventListener pour gérer la validation conditionnelle
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (isset($data['preference']) && $data['preference'] === 'option1') {
                // Activer le champ matricule et rendre obligatoire
                $form->add('matricule', TextType::class, [
                    'label' => 'Votre matricule',
                    'required' => true,
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(['message' => 'Le matricule est obligatoire.']),
                    ],
                ]);
            } else {
                // Activer le champ numéro et rendre obligatoire
                $form->add('numero', TextType::class, [
                    'label' => 'Votre numéro',
                    'required' => true,
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(['message' => 'Le numéro est obligatoire.']),
                    ],
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
