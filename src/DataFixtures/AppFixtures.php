<?php

namespace App\DataFixtures;

use App\Entity\Offer;
use App\Entity\Action;
use App\Entity\Statut;
use App\Entity\Category;
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

        $nameAction = ['Sport en famille', 'Etudiant', 'Ecologie'];

        foreach ($nameAction as $name) {
            $newAction = new Action();
            $newAction->setNameAction($name);

            $manager->persist($newAction);
        }

        // Création des catégories
        $categories = [];
        foreach (
            [
                'Technology',
                'Food',
                'Fashion',
                'Health'
            ] as $categoryName
        ) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Création des offres
        for ($i = 1; $i <= 20; $i++) {
            $offer = new Offer();
            $offer->setTitle('Offer ' . $i);
            $offer->setDescription('Description of Offer ' . $i);

            // Associer une catégorie aléatoire à l'offre
            $randomCategory = $categories[array_rand($categories)];
            $offer->setCategory($randomCategory);

            $manager->persist($offer);
        }


        $manager->flush();
    }
}
