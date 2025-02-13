<?php

namespace App\DataFixtures;

use App\Entity\Offer;
use App\Entity\Action;
use App\Entity\Statut;
use App\Entity\Categories;
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

        // Création des catégories et des offres associées
        $categoriesData = [
            ['label' => 'Catégorie 1', 'code' => 'CAT1', 'offers' => 1],
            ['label' => 'Catégorie 2', 'code' => 'CAT2', 'offers' => 3],
            ['label' => 'Catégorie 3', 'code' => 'CAT3', 'offers' => 1],
            ['label' => 'Catégorie 4', 'code' => 'CAT4', 'offers' => 2],
        ];

        foreach ($categoriesData as $categoryData) {
            $category = new Categories();
            $category->setLabel($categoryData['label']);
            $category->setCode($categoryData['code']);

            $manager->persist($category);

            // Créer les offres associées à cette catégorie
            for ($i = 1; $i <= $categoryData['offers']; $i++) {
                $offer = new Offer();
                $offer->setLabel('Offre ' . $i . ' - ' . $categoryData['label']);
                $offer->setCode('OFF' . $i . '-' . $categoryData['code']);
                $offer->setCategory($category);

                $manager->persist($offer);
            }
        }

        $manager->flush();
    }
}
