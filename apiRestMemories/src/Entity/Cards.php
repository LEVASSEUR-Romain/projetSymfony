<?php

namespace App\Entity;

use App\Repository\CardsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CardsRepository::class)]
class Cards
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $user_id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $front = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $back = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $perso_front = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $perso_back = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getFront(): ?string
    {
        return $this->front;
    }

    public function setFront(string $front): self
    {
        $this->front = $front;

        return $this;
    }

    public function getBack(): ?string
    {
        return $this->back;
    }

    public function setBack(string $back): self
    {
        $this->back = $back;

        return $this;
    }

    public function getPersoFront(): ?string
    {
        return $this->perso_front;
    }

    public function setPersoFront(?string $perso_front): self
    {
        $this->perso_front = $perso_front;

        return $this;
    }

    public function getPersoBack(): ?string
    {
        return $this->perso_back;
    }

    public function setPersoBack(?string $perso_back): self
    {
        $this->perso_back = $perso_back;

        return $this;
    }
}
