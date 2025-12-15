8src/
 └─ Form/
     └─ Cleaner/
         ├─ MainFormCleanerInterface.php
         ├─ ParcoursACleaner.php
         ├─ ParcoursBCleaner.php
         ├─ ParcoursCCleaner.php
         └─ ...



namespace App\Form\Cleaner;

interface MainFormCleanerInterface
{
    /**
     * Retourne TRUE si ce cleaner doit s'appliquer à ce parcours.
     */
    public function supports(array $data): bool;

    /**
     * Modifie les données envoyées par l’utilisateur.
     */
    public function clean(array &$data): void;
}



namespace App\Form\Cleaner;

class ParcoursACleaner implements MainFormCleanerInterface
{
    public function supports(array $data): bool
    {
        return ($data['parcours'] ?? null) === 'A';
    }

    public function clean(array &$data): void
    {
        // On supprime des champs non pertinents
        unset($data['champX']);
        unset($data['champY']);

        // Et on peut faire des règles conditionnelles internes
        if (($data['option_speciale'] ?? null) === 'non') {
            unset($data['champPartage']);
        }
    }
}


services:
    App\Form\Cleaner\:
        resource: '../src/Form/Cleaner'
        tags: ['app.form_cleaner']

class MainFormSubscriber implements EventSubscriberInterface
{
    private iterable $cleaners;

    public function __construct(iterable $cleaners)
    {
        $this->cleaners = $cleaners;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $data = $event->getData();

        foreach ($this->cleaners as $cleaner) {
            if ($cleaner->supports($data)) {
                $cleaner->clean($data);
            }
        }

        $event->setData($data);
    }
}



class MainFormType extends AbstractType
{
    private iterable $cleaners;

    public function __construct(iterable $cleaners)
    {
        $this->cleaners = $cleaners;
    }


services:
    App\Form\MainFormType:
        arguments:
            $cleaners: !tagged_iterator app.form_cleaner

    App\Form\Cleaner\:
        resource: '../src/Form/Cleaner'
        tags: ['app.form_cleaner']

public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
        $data = $event->getData();

        // Appel automatique des cleaners
        foreach ($this->cleaners as $cleaner) {
            if ($cleaner->supports($data)) {
                $cleaner->clean($data);
            }
        }

        $event->setData($data);
    });
}



####

class MainFormType extends AbstractType
{
    public function __construct(
        private iterable $cleaners
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            foreach ($this->cleaners as $cleaner) {
                if ($cleaner->supports($data, $form)) {
                    $cleaner->clean($data, $form);
                }
            }

            $event->setData($data);
        });
    }
}

use Symfony\Component\Form\FormInterface;

interface MainFormCleanerInterface
{
    public function supports(array $data, FormInterface $form): bool;

    public function clean(array &$data, FormInterface $form): void;
}

class PersonalInformationCleaner implements MainFormCleanerInterface
{
    public function __construct(
        private CountryRepository $countryRepository
    ) {}

    public function supports(array $data, FormInterface $form): bool
    {
        return true; // toujours applicable
    }

    public function clean(array &$data, FormInterface $form): void
    {
        if (
            array_key_exists('country', $data)
            && $this->countryRepository->find($data['country'])?->getCode() !== 'FR'
        ) {
            if ($form->has('department')) {
                $data['department'] = null;
            }
        }
    }
}

services:
    App\Form\Cleaner\:
        resource: '../src/Form/Cleaner'
        tags: ['app.form_cleaner']

App\Form\MainFormType:
    arguments:
        $cleaners: !tagged_iterator app.form_cleaner
/**
 * Nettoie les données du formulaire avant le mapping et la validation.
 *
 * - Modifie directement le tableau $data (passé par référence)
 * - Met à null ou vide les champs non pertinents selon le contexte métier
 * - Ne retourne rien
 * - Ne gère PAS la validation ni la configuration du formulaire
 *
 * @param array<string, mixed> $data Données soumises par l'utilisateur (PRE_SUBMIT)
 * @param FormInterface       $form Instance du formulaire courant
 */
public function clean(array &$data, FormInterface $form): void
{
    // ...
}




foreach ($fieldsAddress as $parent => $children) {
    if (!array_key_exists($parent, $data) || !is_array($data[$parent])) {
        continue;
    }

    foreach ($children as $child) {
        if (array_key_exists($child, $data[$parent])) {
            unset($data[$parent][$child]);
        }
    }

    // ⚠️ optionnel : si le sous-tableau devient vide
    if ($data[$parent] === []) {
        unset($data[$parent]);
    }
}




$fieldsAddress = [
    'customer' => [
        'address',
        'addressBis',
        'zipCode',
        'city',
    ],
    'secondBorrow' => [
        'address',
        'addressBis',
        'zipCode',
        'city',
    ],
];

foreach ($fieldsAddress as $parentKey => $children) {

    // sécurité : le parent doit exister dans le form ET dans les data
    if (
        !$form->has($parentKey)
        || !isset($data[$parentKey])
        || !is_array($data[$parentKey])
    ) {
        continue;
    }

    $parentForm = $form->get($parentKey);

    foreach ($children as $child) {

        // sécurité : le champ doit exister dans le form
        if (!$parentForm->has($child)) {
            continue;
        }

        // récupération propre de la config existante
        $config  = $parentForm->get($child)->getConfig();
        $options = $config->getOptions();

        // tu neutralises les contraintes (comme dans ta version)
        $options['constraints'] = [];
        $options['required']    = false;

        // re-add du champ (même logique que toi)
        $parentForm->add(
            $child,
            get_class($config->getType()->getInnerType()),
            $options
        );

        // suppression de la donnée envoyée
        if (array_key_exists($child, $data[$parentKey])) {
            unset($data[$parentKey][$child]);
        }
    }

    // optionnel : si le sous-tableau est vide
    if ($data[$parentKey] === []) {
        unset($data[$parentKey]);
    }
}
