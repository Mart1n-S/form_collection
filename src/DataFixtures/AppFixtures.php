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

        $actions = [
            'Sport en famille',
            'Étudiant',
            'Écologie',
        ];

        // Création des entités Action
        foreach ($actions as $index => $name) {
            $action = new Action();
            $action->setNameAction($name);
            $action->setIndex($index); // Assurez-vous que cette propriété existe dans votre entité

            $manager->persist($action);
        }


        $manager->flush();
    }
}
