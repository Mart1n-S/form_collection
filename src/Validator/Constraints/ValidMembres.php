<?php
// src/Validator/Constraints/ValidMembres.php
namespace App\Validator\Constraints;

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
