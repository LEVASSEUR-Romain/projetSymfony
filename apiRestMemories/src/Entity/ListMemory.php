<?php

namespace App\Entity;

use App\Repository\ListMemoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ListMemoryRepository::class)]
class ListMemory
{
    // variable constrainte
    const MIN_LENGTH_NAME = 2;
    const MAX_LENGTH_NAME = 75;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $user_id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: self::MIN_LENGTH_NAME,
        max: self::MAX_LENGTH_NAME,
        minMessage: 'La taille du nom doit etre supérieur à {{ limit }}',
        maxMessage: 'La taille du nom doit etre inférieur à {{ limit }}',
    )]
    private ?string $name = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
