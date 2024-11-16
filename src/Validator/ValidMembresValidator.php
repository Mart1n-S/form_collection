<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidMembresValidator extends ConstraintValidator
{
    public function validate($association, Constraint $constraint)
    {
        // Récupérer les membres de l'association
        $membres = $association->getMembres();

        // Vérifier le nombre de membres
        $nbMembres = count($membres);
        if ($nbMembres < 1) {
            $this->context->buildViolation($constraint->minMessage)
                ->atPath('membres') // Attacher l'erreur à "membres"
                ->addViolation();
        }

        if ($nbMembres > 3) {
            $this->context->buildViolation($constraint->maxMessage)
                ->atPath('membres') // Attacher l'erreur à "membres"
                ->addViolation();
        }

        // Vérifier s'il y a au moins un Président
        $presidentFound = false;
        foreach ($membres as $membre) {
            if ($membre->getStatut() && $membre->getStatut()->getNom() === 'Président(e)') {
                $presidentFound = true;
                break;
            }
        }

        if (!$presidentFound) {
            $this->context->buildViolation($constraint->presidentMessage)
                ->atPath('membres') // Attacher l'erreur à "membres"
                ->addViolation();
        }
    }
}
