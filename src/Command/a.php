<?php

// src/Command/AnonymizeDataCommand.php

namespace App\Command;

use App\Repository\UserRepository; // À adapter selon tes entités
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:anonymize-data',
    description: 'Anonymise les données personnelles après 1 an pour respecter le RGPD',
)]
class AnonymizeDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository, // Exemple avec une table `User`
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $thresholdDate = (new \DateTimeImmutable())->modify('-1 year');
        $output->writeln('Début de l\'anonymisation...');

        // Exemple avec une entité User
        $usersToAnonymize = $this->userRepository->findOlderThan($thresholdDate);

        foreach ($usersToAnonymize as $user) {
            $user->setEmail('anonyme@example.com');
            $user->setFirstname('Anonyme');
            $user->setLastname('Utilisateur');
            $user->setPhone(null);
            $user->setIsAnonymized(true); // Si tu veux une trace
        }

        $this->em->flush();
        $output->writeln(count($usersToAnonymize) . ' utilisateur(s) anonymisé(s).');

        return Command::SUCCESS;
    }
}
