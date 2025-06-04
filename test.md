// src/Service/Anonymizer/AnonymizableInterface.php

namespace App\Service\Anonymizer;

interface AnonymizableInterface
{
    public function anonymizeOldRecords(\DateTimeInterface $thresholdDate): int;
}


// src/Service/Anonymizer/UserAnonymizer.php

namespace App\Service\Anonymizer;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserAnonymizer implements AnonymizableInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {}

    public function anonymizeOldRecords(\DateTimeInterface $thresholdDate): int
    {
        $users = $this->userRepository->createQueryBuilder('u')
            ->where('u.createdAt < :date')
            ->andWhere('u.isAnonymized = false')
            ->setParameter('date', $thresholdDate)
            ->getQuery()
            ->getResult();

        foreach ($users as $user) {
            $user->setEmail('anonyme@example.com');
            $user->setFirstname('Anonyme');
            $user->setLastname('Utilisateur');
            $user->setPhone(null);
            $user->setIsAnonymized(true);
        }

        $this->em->flush();
        return count($users);
    }
}


// src/Command/AnonymizeDataCommand.php

namespace App\Command;

use App\Service\Anonymizer\AnonymizableInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:anonymize-data',
    description: 'Anonymise les données personnelles dans plusieurs entités',
)]
class AnonymizeDataCommand extends Command
{
    /**
     * @param iterable<AnonymizableInterface> $anonymizers
     */
    public function __construct(
        private iterable $anonymizers, // Injecté automatiquement par autowiring
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $thresholdDate = (new \DateTimeImmutable())->modify('-1 year');

        $output->writeln('Début de l\'anonymisation globale...');

        foreach ($this->anonymizers as $anonymizer) {
            $count = $anonymizer->anonymizeOldRecords($thresholdDate);
            $output->writeln("➤ {$count} enregistrement(s) anonymisé(s) par " . $anonymizer::class);
        }

        $output->writeln('Fin de l\'anonymisation.');
        return Command::SUCCESS;
    }
}



# config/services.yaml
services:
    App\Service\Anonymizer\:
        resource: '../src/Service/Anonymizer/'
        tags: ['app.anonymizer']




        L’adresse de votre association doit contenir uniquement des lettres, chiffres, espaces et ponctuations simples (exemples : virgule, apostrophe, tiret). Exemple : 12 rue des Lilas."



new Regex([
            'pattern' => '/^[a-zA-Z0-9._%+\-]{1,30}@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
            'message' => 'Format invalide. Exemple : jane.doe@email.com.',
        ]),




'pattern' => '/^(?=.{6,120}$)[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/'

new Regex([
            'pattern' => '/^(?=.{6,120}$)[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/',
            'message' => 'L’adresse email doit être valide et contenir entre 6 et 120 caractères.',
        ]),

