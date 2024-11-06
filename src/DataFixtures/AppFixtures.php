<?php

namespace App\DataFixtures;

use App\Entity\Action;
use App\Entity\Statut;
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


        $manager->flush();
    }
}
