<?php

namespace App\Command;

use App\Entity\Demo;
use App\Repository\DemoRepository;
use App\Service\ExportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Commande qui nettoie les fichiers etape_*.html et relance l'export.
 */
#[AsCommand(
    name: 'app:reexport-demos',
    description: 'Supprime les anciens fichiers etape_ et régénère les exports pour chaque demo.'
)]
class ReexportDemosCommand extends Command
{
    private DemoRepository $demoRepository;
    private ExportService $exportService;
    private string $publicDir;

    public function __construct(
        DemoRepository $demoRepository,
        ExportService $exportService,
        string $projectDir
    ) {
        parent::__construct();
        $this->demoRepository = $demoRepository;
        $this->exportService = $exportService;
        $this->publicDir = $projectDir . '/public';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();

        // On récupère tous les demos
        $demos = $this->demoRepository->findAll();

        foreach ($demos as $demo) {
            /** @var Demo $demo */
            $demoId = $demo->getId();
            $demoDir = $this->publicDir . "/demos/{$demoId}";

            if (!is_dir($demoDir)) {
                $output->writeln("<comment>Dossier inexistant pour demo {$demoId}, on skip.</comment>");
                continue;
            }

            // On supprime tous les fichiers etape_*.html
            $files = glob($demoDir . '/etape_*.html');

            foreach ($files as $file) {
                $filesystem->remove($file);
                $output->writeln("<info>Supprimé: {$file}</info>");
            }

            // On relance l'export
            try {
                $this->exportService->export($demo);
                $output->writeln("<info>Export recréé pour demo {$demoId}</info>");
            } catch (\Throwable $e) {
                $output->writeln("<error>Erreur export demo {$demoId}: {$e->getMessage()}</error>");
            }
        }

        $output->writeln('<info>Opération terminée.</info>');

        return Command::SUCCESS;
    }
}







services:
    App\Command\ReexportDemosCommand:
        arguments:
            $projectDir: '%kernel.project_dir%'







<?php

namespace App\Command;

use App\Entity\Demo;
use App\Repository\DemoRepository;
use App\Service\ExportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Commande qui nettoie les fichiers etape_*.html et relance l'export.
 */
#[AsCommand(
    name: 'app:reexport-demos',
    description: 'Supprime les anciens fichiers etape_ et régénère les exports pour chaque demo.'
)]
class ReexportDemosCommand extends Command
{
    private DemoRepository $demoRepository;
    private ExportService $exportService;
    private string $publicDir;

    public function __construct(
        DemoRepository $demoRepository,
        ExportService $exportService,
        string $projectDir
    ) {
        parent::__construct();
        $this->demoRepository = $demoRepository;
        $this->exportService = $exportService;
        $this->publicDir = $projectDir . '/public';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();

        // Récupération de tous les demos
        $demos = $this->demoRepository->findAll();

        foreach ($demos as $demo) {
            /** @var Demo $demo */
            $demoId = $demo->getId();
            $demoDir = $this->publicDir . "/demos/{$demoId}";

            if (!is_dir($demoDir)) {
                $output->writeln("<comment>Dossier inexistant pour demo {$demoId}, on skip.</comment>");
                continue;
            }

            // Utilisation de Finder pour chercher les fichiers à supprimer
            $finder = new Finder();
            $finder->files()
                ->in($demoDir)
                ->name('etape_*.html');

            $count = 0;

            foreach ($finder as $file) {
                $filesystem->remove($file->getRealPath());
                $output->writeln("<info>Supprimé: {$file->getRelativePathname()}</info>");
                $count++;
            }

            $output->writeln("<info>{$count} fichier(s) supprimé(s) pour demo {$demoId}.</info>");

            // Relancer l'export
            try {
                $this->exportService->export($demo);
                $output->writeln("<info>Export recréé pour demo {$demoId}</info>");
            } catch (\Throwable $e) {
                $output->writeln("<error>Erreur export demo {$demoId}: {$e->getMessage()}</error>");
            }
        }

        $output->writeln('<info>Opération terminée.</info>');

        return Command::SUCCESS;
    }
}
