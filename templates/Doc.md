private function removeConstraints(FormInterface $form, array $fields, array &$data): void
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

