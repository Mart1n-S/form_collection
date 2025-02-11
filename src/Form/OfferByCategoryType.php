<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class OfferByCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = $options['categories'];

        foreach ($categories as $category) {
            $builder->add('category_' . $category->getId(), EntityType::class, [
                'class' => Offer::class,
                'choices' => $category->getOffers(), // Les offres de cette catégorie
                'choice_label' => 'title',          // Affiche le titre des offres
                'multiple' => true,                 // Permet de sélectionner plusieurs offres
                'expanded' => true,                 // Affiche les choix sous forme de checkbox
                'label' => $category->getName(),    // Affiche le nom de la catégorie
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'categories' => [],
        ]);
    }
}
