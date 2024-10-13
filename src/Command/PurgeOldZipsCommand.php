<?php

// src/Command/PurgeOldZipsCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

// config cron job : 0 0 * * * /usr/bin/php /chemin/de/ton/projet/bin/console app:purge-old-zips --days=7

class PurgeOldZipsCommand extends Command
{
    protected static $defaultName = 'app:purge-old-zips';
    private $uploadDirectory;

    public function __construct(string $uploadDirectory)
    {
        $this->uploadDirectory = $uploadDirectory;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Purge les fichiers ZIP non téléchargés dans un certain délai (en minutes)')
            ->addOption('minutes', null, InputOption::VALUE_OPTIONAL, 'Nombre de minutes avant de purger les fichiers ZIP', 60);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();
        $minutes = (int) $input->getOption('minutes');
        $now = time();
        $purgeBefore = $now - ($minutes * 60); // Convertir les minutes en secondes

        // Démarrer la mesure du temps
        $startTime = microtime(true);

        // Parcourir les dossiers dans le répertoire uploads
        $folders = glob($this->uploadDirectory . '/*', GLOB_ONLYDIR);

        // Initialiser la barre de progression
        $io->progressStart(count($folders));

        foreach ($folders as $folder) {
            $zipFile = $folder . '/documents.zip';

            // Vérifier si le fichier ZIP existe
            if (file_exists($zipFile)) {
                $fileCreationTime = filemtime($zipFile);

                // Si le fichier a été créé avant la date limite de purge, supprimer le dossier
                if ($fileCreationTime < $purgeBefore) {
                    $io->note("Suppression du répertoire : " . $folder);
                    $filesystem->remove($folder);
                }
            }

            // Mettre à jour la barre de progression
            $io->progressAdvance();
        }

        // Fin de la mesure du temps
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Terminer la barre de progression
        $io->progressFinish();

        $io->success('Purge des anciens fichiers ZIP terminée.');
        $io->success(sprintf('Temps de traitement : %.2f secondes', $executionTime));

        return Command::SUCCESS;
    }
}
