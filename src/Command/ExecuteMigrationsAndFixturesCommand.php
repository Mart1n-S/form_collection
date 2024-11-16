<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class ExecuteMigrationsAndFixturesCommand extends Command
{
    protected static $defaultName = 'app:run-migrations-and-fixtures';

    protected function configure()
    {
        $this->setDescription('Exécute les migrations et charge les fixtures dans la base de données.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Exécution des migrations
        $output->writeln('Exécution des migrations...');

        // Création de l'entrée pour la commande doctrine:migrations:migrate
        $migrateCommand = $this->getApplication()->find('doctrine:migrations:migrate');
        $migrateCommandInput = new ArrayInput([
            'command' => 'doctrine:migrations:migrate'
        ]);

        // Simuler la réponse "yes" en réorientant l'entrée
        $migrateCommandInput->setInteractive(false); // Désactive toute interaction
        $migrateCommand->run($migrateCommandInput, $output);

        // Chargement des fixtures
        $output->writeln('Chargement des fixtures...');

        // Création de l'entrée pour la commande doctrine:fixtures:load
        $fixturesCommand = $this->getApplication()->find('doctrine:fixtures:load');
        $fixturesCommandInput = new ArrayInput([
            'command' => 'doctrine:fixtures:load',
        ]);

        // Simuler la réponse "yes" en réorientant l'entrée
        $fixturesCommandInput->setInteractive(false); // Désactive toute interaction
        $fixturesCommand->run($fixturesCommandInput, $output);

        return Command::SUCCESS;
    }
}
