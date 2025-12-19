use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormInterface;

$builder->setEmptyData(function (FormInterface $form) {

    $hasValue = false;

    foreach ($form as $child) {
        $data = $child->getData();

        // 1️⃣ Cas collection (types)
        if ($data instanceof Collection) {
            if (!$data->isEmpty()) {
                $hasValue = true;
                break;
            }
            continue;
        }

        // 2️⃣ Cas champ number : 0 est une valeur valide
        if ($data === 0 || $data === '0') {
            $hasValue = true;
            break;
        }

        // 3️⃣ Cas champ classique
        if ($data !== null && $data !== '') {
            $hasValue = true;
            break;
        }
    }

    return $hasValue ? new OtherIncome() : null;
});










###mresubmit

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

$builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {

    $data = $event->getData();

    if (!isset($data['otherIncome'])) {
        return;
    }

    $amount = $data['otherIncome']['totalAmount'] ?? null;
    $types  = $data['otherIncome']['types'] ?? [];

    $amountIsEmpty = ($amount === null || $amount === '');
    $typesIsEmpty  = empty($types);

    if ($amountIsEmpty && $typesIsEmpty) {
        // 🔥 clé du fix
        $data['otherIncome'] = null;
    }

    $event->setData($data);
});




<script>
document.addEventListener('DOMContentLoaded', function () {

    // 1️⃣ Création du bouton
    const button = document.createElement('button');
    button.textContent = '🧪 Auto-fill (dev)';
    button.style.position = 'fixed';
    button.style.bottom = '20px';
    button.style.right = '20px';
    button.style.zIndex = '9999';
    button.style.padding = '10px 15px';
    button.style.cursor = 'pointer';

    document.body.appendChild(button);

    // 2️⃣ Click handler
    button.addEventListener('click', function () {

        // Sections visibles uniquement
        const visibleSections = Array.from(document.querySelectorAll('section'))
            .filter(section => section.offsetParent !== null);

        visibleSections.forEach(section => {

            // INPUTS
            section.querySelectorAll('input').forEach(input => {

                if (input.disabled || input.readOnly) {
                    return;
                }

                switch (input.type) {
                    case 'text':
                        input.value ||= 'Test';
                        break;

                    case 'email':
                        input.value ||= 'test@example.com';
                        break;

                    case 'number':
                        input.value ||= 0;
                        break;

                    case 'radio':
                        if (!document.querySelector(`input[name="${input.name}"]:checked`)) {
                            input.checked = true;
                        }
                        break;

                    case 'checkbox':
                        input.checked = true;
                        break;
                }

                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            // SELECTS
            section.querySelectorAll('select').forEach(select => {
                if (select.disabled) {
                    return;
                }

                if (select.options.length > 1) {
                    select.selectedIndex = 1;
                }

                select.dispatchEvent(new Event('change', { bubbles: true }));
            });

            // TEXTAREA
            section.querySelectorAll('textarea').forEach(textarea => {
                if (!textarea.disabled) {
                    textarea.value ||= 'Test';
                    textarea.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });

        console.log('✅ Auto-fill terminé');
    });
});
</script>

