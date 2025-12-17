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
