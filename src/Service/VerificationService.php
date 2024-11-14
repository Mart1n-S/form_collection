<?php

namespace App\Service;

class VerificationService
{
    private string $env;

    public function __construct(string $env)
    {
        $this->env = $env;
    }

    public function verifySomething()
    {
        // Si l'environnement est "dev", on n'exécute pas la vérification
        if ($this->env === 'dev') {
            return 'env de dev';
        }

        return 'env de prod';
    }
}
