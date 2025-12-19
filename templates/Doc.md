function openModalIfHasErrors() {
    const modalEl = document.getElementById('consultant_modal');
    if (!modalEl) return;

    // Erreur Symfony dans la modal ?
    const hasErrors = modalEl.querySelector('.invalid-feedback');

    if (hasErrors) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}


function initForm() {
    let $section = stepWithFirstError();

    if ($section) {
        $section.removeClass("d-none");
    } else {
        $('.piafs').removeClass('d-none');
    }

    $('.home-page').addClass('d-none');
    $('#container-stepper').removeClass('d-none');

    // 🔥 Ouvre la modal si une erreur s'y trouve
    openModalIfHasErrors();
}



if (input.name && !input.name.endsWith('[firstname]')) {
    // OK
}



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



<script>
document.addEventListener('DOMContentLoaded', function () {

    const button = document.createElement('button');
    button.textContent = '🧪 Auto-fill (dev)';
    button.style.position = 'fixed';
    button.style.bottom = '20px';
    button.style.right = '20px';
    button.style.zIndex = '9999';
    button.style.padding = '10px 15px';

    document.body.appendChild(button);

    button.addEventListener('click', function () {

        const visibleSections = Array.from(document.querySelectorAll('section'))
            .filter(section => section.offsetParent !== null);

        visibleSections.forEach(section => {

            section.querySelectorAll('input').forEach(input => {
                if (input.disabled || input.readOnly) return;
                if (input.value) return;

                let value = '';

                // 👉 TEXT (mais potentiellement number masqué)
                if (input.type === 'text') {

                    if (input.classList.contains('currency')) {
                        value = 1000;
                    } else if (input.maxLength > 0) {
                        value = '1'.repeat(Math.min(3, input.maxLength));
                    } else {
                        value = 'Test';
                    }
                }

                // 👉 EMAIL
                if (input.type === 'email') {
                    value = 'test@example.com';
                }

                // 👉 NUMBER
                if (input.type === 'number') {
                    const min = input.min !== '' ? Number(input.min) : 0;
                    value = min;
                }

                // 👉 RADIO
                if (input.type === 'radio') {
                    if (!document.querySelector(`input[name="${input.name}"]:checked`)) {
                        input.checked = true;
                    }
                    return;
                }

                // 👉 CHECKBOX
                if (input.type === 'checkbox') {
                    input.checked = true;
                    return;
                }

                if (value !== '') {
                    input.value = value;

                    // 🔥 Important pour les masks
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

            // 👉 SELECT
            section.querySelectorAll('select').forEach(select => {
                if (select.disabled) return;

                if (select.selectedIndex === 0 && select.options.length > 1) {
                    select.selectedIndex = 1;
                }

                select.dispatchEvent(new Event('change', { bubbles: true }));
            });

            // 👉 TEXTAREA
            section.querySelectorAll('textarea').forEach(textarea => {
                if (!textarea.disabled && !textarea.value) {
                    textarea.value = 'Test';
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    textarea.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });

        console.log('✅ Auto-fill terminé');
    });
});
</script>






section.querySelectorAll('input').forEach(input => {
    if (input.disabled || input.readOnly) return;

    // 👉 RADIO
    if (input.type === 'radio') {
        if (!document.querySelector(`input[name="${input.name}"]:checked`)) {
            input.checked = true;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
        return;
    }

    // 👉 CHECKBOX
    if (input.type === 'checkbox') {
        input.checked = true;
        input.dispatchEvent(new Event('change', { bubbles: true }));
        return;
    }

    // ⛔ maintenant seulement on ignore les champs déjà remplis
    if (input.value) return;

    let value = '';

    // 👉 TEXT (potentiellement numérique masqué)
    if (input.type === 'text') {
        if (input.classList.contains('currency')) {
            value = 1000;
        } else if (input.maxLength > 0) {
            value = '1'.repeat(Math.min(3, input.maxLength));
        } else {
            value = 'Test';
        }
    }

    // 👉 EMAIL
    if (input.type === 'email') {
        value = 'test@example.com';
    }

    // 👉 NUMBER
    if (input.type === 'number') {
        const min = input.min !== '' ? Number(input.min) : 0;
        value = min;
    }

    if (value !== '') {
        input.value = value;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
});


function getDateValueForInput(input) {
    const pattern = input.getAttribute('pattern') || '';
    const maxLength = input.maxLength;

    // Pattern explicite
    if (pattern.includes('\\d{2}/\\d{2}/\\d{4}')) {
        return '01/01/2000';
    }

    if (pattern.includes('\\d{2}/\\d{4}')) {
        return '01/2000';
    }

    // Fallback par longueur
    if (maxLength === 7) {      // MM/YYYY
        return '01/2000';
    }

    if (maxLength === 10) {     // DD/MM/YYYY
        return '01/01/2000';
    }

    // Fallback safe
    return '01/01/2000';
}


// 👉 TEXT (y compris number masqué et date)
if (input.type === 'text') {

    // 🔥 DATE
    if (input.dataset.type === 'date') {
        value = getDateValueForInput(input);
    }

    // 💰 CURRENCY
    else if (input.classList.contains('currency')) {
        value = 1000;
    }

    // 🔢 TEXT avec maxlength
    else if (input.maxLength > 0) {
        value = '1'.repeat(Math.min(3, input.maxLength));
    }

    // 📝 TEXTE CLASSIQUE
    else {
        value = 'Test';
    }
}
