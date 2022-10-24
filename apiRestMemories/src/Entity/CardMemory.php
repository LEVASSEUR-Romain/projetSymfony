<?php

namespace App\Entity;

use App\Repository\CardMemoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardMemoryRepository::class)]
class CardMemory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'cardMemories')]
    private ?self $memory_id = null;

    #[ORM\OneToMany(mappedBy: 'memory_id', targetEntity: self::class)]
    private Collection $cardMemories;

    #[ORM\ManyToOne(inversedBy: 'cardMemories')]
    private ?Cards $card_id = null;

    public function __construct()
    {
        $this->cardMemories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMemoryId(): ?self
    {
        return $this->memory_id;
    }

    public function setMemoryId(?self $memory_id): self
    {
        $this->memory_id = $memory_id;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCardMemories(): Collection
    {
        return $this->cardMemories;
    }

    public function addCardMemory(self $cardMemory): self
    {
        if (!$this->cardMemories->contains($cardMemory)) {
            $this->cardMemories->add($cardMemory);
            $cardMemory->setMemoryId($this);
        }

        return $this;
    }

    public function removeCardMemory(self $cardMemory): self
    {
        if ($this->cardMemories->removeElement($cardMemory)) {
            // set the owning side to null (unless already changed)
            if ($cardMemory->getMemoryId() === $this) {
                $cardMemory->setMemoryId(null);
            }
        }

        return $this;
    }

    public function getCardId(): ?Cards
    {
        return $this->card_id;
    }

    public function setCardId(?Cards $card_id): self
    {
        $this->card_id = $card_id;

        return $this;
    }
}
