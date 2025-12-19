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
