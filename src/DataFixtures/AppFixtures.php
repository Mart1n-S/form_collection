<?php

namespace App\DataFixtures;

use App\Entity\Action;
use App\Entity\Statut;
use App\Entity\Categorie;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer une boucle pour créer 3 statuts
        $statuts = ['Président(e)', 'Secrétaire', 'Trésorier(e)'];

        foreach ($statuts as $statut) {
            $newStatut = new Statut();
            $newStatut->setNom($statut);

            $manager->persist($newStatut);
        }

        $categories = ['Sport', 'Culture', 'Humanitaire'];

        // Stocker les entités Categorie pour les réutiliser
        $categoryEntities = [];

        foreach ($categories as $categoryName) {
            $newCategory = new Categorie();
            $newCategory->setName($categoryName);

            $manager->persist($newCategory);
            $categoryEntities[] = $newCategory; // On sauvegarde l'entité dans un tableau
        }

        $actions = [
            'Sport en famille' => 'Sport',  // Associe une catégorie
            'Tournoi de foot' => 'Sport',    // Associe une catégorie
            'Cours de danse' => 'Culture',   // Associe une catégorie
            'Étudiant' => 'Culture',       // Associe une catégorie
            'Collecte de vêtements' => 'Humanitaire', // Associe une catégorie
            'Écologie' => 'Humanitaire',   // Associe une catégorie
        ];

        // Création des entités Action
        foreach ($actions as $index => $categoryName) {
            $action = new Action();
            $action->setNameAction($index);
            $action->setIndex(array_search($index, array_keys($actions))); // Définit un index unique

            // Trouver la catégorie correspondante
            $category = array_filter($categoryEntities, function ($cat) use ($categoryName) {
                return $cat->getName() === $categoryName;
            });
            $category = reset($category); // Obtenir la première correspondance

            if ($category) {
                $action->setCategorie($category); // Associe l'action à la catégorie
            }

            $manager->persist($action);
        }

        $manager->flush();
    }
}
