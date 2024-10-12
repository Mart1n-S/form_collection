<?php

namespace App\DataFixtures;

use App\Entity\Statut;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

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

        $manager->flush();
    }
}
