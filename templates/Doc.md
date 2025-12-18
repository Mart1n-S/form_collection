8private function removeConstraints(FormInterface $form, array $fields, array &$data): void
{
    foreach ($fields as $field) {
        if (!$form->has($field)) {
            continue;
        }

        $config = $form->get($field)->getConfig();
        $options = $config->getOptions();
        $options['constraints'] = [];

        $form->add(
            $field,
            get_class($config->getType()->getInnerType()),
            $options
        );

        unset($data['customer'][$field]);
    }
}


$customerForm = $form->get('customer');

if (in_array($customerContractTypeCode, ['CDI', 'CDD'], true)) {

    if ($customerContractTypeCode === 'CDI') {
        $this->removeConstraints(
            $customerForm,
            ['isTemporaryJob'],
            $data
        );
    }

    $this->removeConstraints(
        $customerForm,
        ['proJob', 'proAnnualIncomeAmount', 'otherJob', 'otherIncomeAmount'],
        $data
    );

} else {

    $this->removeConstraints(
        $customerForm,
        ['job', 'employer', 'hireDate', 'monthlyTaxableSalaryAmount', 'isTemporaryJob'],
        $data
    );
}

if ($customerContractTypeCode === 'Professionnel ou agricole') {
    $this->removeConstraints(
        $customerForm,
        ['otherJob', 'otherIncomeAmount'],
        $data
    );
}


$rules = [
    'CDI' => [
        'remove' => [
            'isTemporaryJob',
            'proJob',
            'proAnnualIncomeAmount',
            'otherJob',
            'otherIncomeAmount',
        ],
    ],
    'CDD' => [
        'remove' => [
            'proJob',
            'proAnnualIncomeAmount',
            'otherJob',
            'otherIncomeAmount',
        ],
    ],
    'default' => [
        'remove' => [
            'job',
            'employer',
            'hireDate',
            'monthlyTaxableSalaryAmount',
            'isTemporaryJob',
        ],
    ],
    'Professionnel ou agricole' => [
        'remove' => [
            'otherJob',
            'otherIncomeAmount',
        ],
    ],
];

$key = $rules[$customerContractTypeCode] ?? $rules['default'];

$this->removeConstraints($customerForm, $key['remove'], $data);



final class CleanContext
{
    public function __construct(
        public readonly array $data,
        public readonly ?FormInterface $form = null,
        public readonly ?string $contractType = null,
        public readonly bool $isProAgri = false,
    ) {}
}



interface CleanerInterface
{
    public function clean(CleanContext $context): void;
}

class SimpleCleaner implements CleanerInterface
{
    public function clean(CleanContext $context): void
    {
        // utilise seulement data
        $data = $context->data;
    }
}


class ContractCleaner implements CleanerInterface
{
    public function clean(CleanContext $context): void
    {
        if ($context->contractType === 'CDI') {
            // ...
        }
    }
}


$context = new CleanContext(
    data: $data,
    form: $form,
    contractType: $contractType,
    isProAgri: $isProAgri
);

foreach ($cleaners as $cleaner) {
    $cleaner->clean($context);
}



function resetLoansCollectionOnSkip(loansFieldsContainer) {
    const items = loansFieldsContainer.querySelectorAll('.loan-item');

    // Tant qu'il y a plus d'un item → on supprime
    while (items.length > 1) {
        const lastItem = loansFieldsContainer.querySelector('.loan-item:last-child');

        if (!lastItem) {
            break;
        }

        const button = lastItem.querySelector('[data-action="remove"]');

        // sécurité
        if (button) {
            toggleButtonResetOrDelete(button);
        }

        lastItem.remove();
    }

    // À la fin, on s'assure que le dernier bouton est en mode "reset"
    const remainingItem = loansFieldsContainer.querySelector('.loan-item');
    if (remainingItem) {
        const button = remainingItem.querySelector('[data-action="remove"]');
        if (button) {
            toggleButtonResetOrDelete(button);
        }
    }
}



function resetLoansCollectionOnSkip($loansFieldsContainer) {
    const $items = $loansFieldsContainer.find('.loan-item');

    // On garde le premier, on supprime les autres
    for (let i = $items.length - 1; i >= 1; i--) {
        const $item = $items.eq(i);
        const $button = $item.find('[data-action="remove"]');

        if ($button.length) {
            toggleButtonResetOrDelete($button);
        }

        $item.remove();
    }

    // Remet le bouton du dernier item au bon état
    const $remainingItem = $loansFieldsContainer.find('.loan-item').first();
    if ($remainingItem.length) {
        const $button = $remainingItem.find('[data-action="remove"]');
        if ($button.length) {
            toggleButtonResetOrDelete($button);
        }
    }
}


$(document).ready(function () {

    const target = document.querySelector('section.loans-charges');

    if (!target) {
        return;
    }

    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {

            if (
                mutation.type === 'attributes'
                && mutation.attributeName === 'class'
                && target.classList.contains('skip')
            ) {
                resetLoansCollectionOnSkip($(target));
            }
        });
    });

    observer.observe(target, {
        attributes: true,
        attributeFilter: ['class'],
    });

});

/**
 * Observe la section `.loans-charges` pour détecter l'ajout de la classe `skip`.
 * Quand l'étape est skippée, on réinitialise la collection des prêts
 * (suppression des items en trop + reset des boutons).
 *
 * Utilise un MutationObserver car la classe est ajoutée dynamiquement.
 */



Donc cela est validé :

---

# 🧠 Récap métier (validé)

* **Client**

  * a **1 et 1 seul** `AutreRevenu` (total)
* **AutreRevenu**

  * appartient à **1 et 1 seul** client
  * contient **le montant total**
  * peut avoir **0 à N catégories de revenu**
* **AutreRevenuType**

  * peut appartenir à **0 à N AutreRevenu**

👉 Donc :

* Client ↔ AutreRevenu = **OneToOne**
* AutreRevenu ↔ AutreRevenuType = **ManyToMany**

---

# ⚠️ Point critique (cause de tes lignes vides)

👉 **Un `AutreRevenu` ne doit PAS être créé si aucun type n’est sélectionné**
👉 **La relation ManyToMany doit être vide, pas partiellement créée**

---

# ✅ Modèle PROPRE et CORRECT

## 1️⃣ Client

```php
#[ORM\Entity]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(
        mappedBy: 'client',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private ?AutreRevenu $autreRevenu = null;

    public function setAutreRevenu(?AutreRevenu $autreRevenu): void
    {
        $this->autreRevenu = $autreRevenu;

        if ($autreRevenu !== null) {
            $autreRevenu->setClient($this);
        }
    }

    public function getAutreRevenu(): ?AutreRevenu
    {
        return $this->autreRevenu;
    }
}
```

✔️ nullable
✔️ orphanRemoval
✔️ pas de création forcée

---

## 2️⃣ AutreRevenu (le total)

```php
#[ORM\Entity]
class AutreRevenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $totalAmount = 0;

    #[ORM\OneToOne(inversedBy: 'autreRevenu')]
    #[ORM\JoinColumn(nullable: false)]
    private Client $client;

    #[ORM\ManyToMany(targetEntity: AutreRevenuType::class)]
    #[ORM\JoinTable(name: 'autre_revenu_type_link')]
    private Collection $types;

    public function __construct()
    {
        $this->types = new ArrayCollection();
    }

    public function addType(AutreRevenuType $type): void
    {
        if (!$this->types->contains($type)) {
            $this->types->add($type);
        }
    }

    public function removeType(AutreRevenuType $type): void
    {
        $this->types->removeElement($type);
    }

    public function isEmpty(): bool
    {
        return $this->types->isEmpty();
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
```

✔️ `ManyToMany` simple
✔️ pas de ligne vide possible dans la table de jointure
✔️ `isEmpty()` clé pour le nettoyage

---

## 3️⃣ AutreRevenuType (référentiel)

```php
#[ORM\Entity]
class AutreRevenuType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $label;
}
```

✔️ simple
✔️ stable
✔️ pas besoin de relation inverse (optionnelle)

---

# 🔥 LE point CLÉ pour éviter les lignes vides

## 👉 AVANT le `flush`

```php
$autreRevenu = $client->getAutreRevenu();

if ($autreRevenu !== null && $autreRevenu->isEmpty()) {
    $client->setAutreRevenu(null);
}
```

👉 Résultat :

* ❌ pas de ligne `autre_revenu`
* ❌ pas de ligne `autre_revenu_type_link`
* ✔️ base propre

---

# ❌ Ce qu’il ne faut PAS faire

❌ Créer `AutreRevenu` dès que le formulaire existe
❌ Stocker des types sans vérifier la sélection
❌ Rendre la relation Client → AutreRevenu non nullable côté Client
❌ Mettre `cascade persist` sans `orphanRemoval`

---

# 🧪 Cas validés

| Cas                    | Résultat                    |
| ---------------------- | --------------------------- |
| Aucun type sélectionné | ❌ aucune ligne              |
| 1 type sélectionné     | ✔️ 1 autre_revenu + 1 lien  |
| 3 types sélectionnés   | ✔️ 1 autre_revenu + 3 liens |
| Suppression            | ✔️ suppression propre       |

---

