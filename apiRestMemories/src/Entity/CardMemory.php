<?php

namespace App\Entity;

use App\Repository\CardMemoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CardMemoryRepository::class)]
class CardMemory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $memory_id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $card_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMemoryId(): ?int
    {
        return $this->memory_id;
    }

    public function setMemoryId(int $memory_id): self
    {
        $this->memory_id = $memory_id;

        return $this;
    }

    public function getCardId(): ?int
    {
        return $this->card_id;
    }

    public function setCardId(int $card_id): self
    {
        $this->card_id = $card_id;

        return $this;
    }
}
