<?php

namespace App\Entity;

use App\Repository\DowloadTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DowloadTokenRepository::class)]
class DowloadToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column(length: 255)]
    private ?string $folderPath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getFolderPath(): ?string
    {
        return $this->folderPath;
    }

    public function setFolderPath(string $folderPath): static
    {
        $this->folderPath = $folderPath;

        return $this;
    }
}
