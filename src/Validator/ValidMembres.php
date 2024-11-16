<?php
// Fix retirer changer App\Validator\Consraint par App\Validator car le fichier se trouve dans le dossier Validator et plus dans le dossier Validator/Constraint => idem pour l'appek dans l'entité
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ValidMembres extends Constraint
{
    public $minMessage = 'L\'association doit avoir au moins 1 membre.';
    public $maxMessage = 'L\'association ne peut pas avoir plus de 3 membres.';
    public $presidentMessage = 'L\'association doit avoir au moins un Président.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
