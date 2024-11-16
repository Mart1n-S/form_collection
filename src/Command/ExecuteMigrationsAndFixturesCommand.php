<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\DBAL\Connection;

class ExecuteMigrationsAndFixturesCommand extends Command
{
    protected static $defaultName = 'app:run-migrations-and-fixtures';

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Exécute les migrations, crée la base de données (si nécessaire), et charge les fixtures.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Initialisation de SymfonyStyle pour des sorties plus lisibles
        $io = new SymfonyStyle($input, $output);

        // 1. Vérification si la base de données existe
        $io->section('Vérification de l\'existence de la base de données...');

        $dbCreated = false;
        // Vérifier si la base de données existe
        try {
            $dbName = $this->connection->getDatabase();
            $this->connection->executeQuery("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'")->fetchAssociative();
            $io->warning('La base de données existe déjà. Passons aux étapes suivantes.');
        } catch (\Exception $e) {
            $io->error('Erreur lors de la vérification de l\'existence de la base de données : ' . $e->getMessage());
            $dbExists = false;
        }

        if (isset($dbExists) && !$dbExists) {
            try {
                // Si la base n'existe pas, on tente de la créer
                $createDbCommand = $this->getApplication()->find('doctrine:database:create');
                $createDbCommandInput = new ArrayInput([
                    'command' => 'doctrine:database:create',
                ]);
                $createDbCommand->run($createDbCommandInput, $output);
                $dbCreated = true;
                $io->success('Base de données créée avec succès !');
            } catch (\Exception $e) {
                $io->error('Erreur lors de la création de la base de données : ' . $e->getMessage());
            }
        }

        // 2. Exécution des migrations
        $io->section('Exécution des migrations...');

        $migrateCommand = $this->getApplication()->find('doctrine:migrations:migrate');
        $migrateCommandInput = new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
        ]);

        // Simuler la réponse "yes" en réorientant l'entrée en désactivant l'interaction
        $migrateCommandInput->setInteractive(false);
        $migrateCommand->run($migrateCommandInput, $output);
        $io->success('Migrations exécutées avec succès.');

        // 3. Chargement des fixtures (seulement si la base a été créée)
        if ($dbCreated) {
            // Rafraîchissement de la connexion à la base de données
            $this->connection->close();
            $this->connection->connect();
            $io->success('Connexion réinitialisée avec succès après la création de la base de données.');

            $io->section('Chargement des fixtures...');
            $fixturesCommand = $this->getApplication()->find('doctrine:fixtures:load');
            $fixturesCommandInput = new ArrayInput([
                'command' => 'doctrine:fixtures:load',
            ]);

            // Simuler la réponse "yes" en réorientant l'entrée en désactivant l'interaction
            $fixturesCommandInput->setInteractive(false);
            $fixturesCommand->run($fixturesCommandInput, $output);
            $io->success('Les fixtures ont été chargées avec succès !');
        }

        return Command::SUCCESS;
    }
}
