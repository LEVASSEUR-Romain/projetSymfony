<?php

namespace App\Entity;

use App\Repository\CardsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardsRepository::class)]
class Cards
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    private ?User $user_id = null;

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

    #[ORM\OneToMany(mappedBy: 'card_id', targetEntity: CardMemory::class)]
    private Collection $cardMemories;

    public function __construct()
    {
        $this->cardMemories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
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

    /**
     * @return Collection<int, CardMemory>
     */
    public function getCardMemories(): Collection
    {
        return $this->cardMemories;
    }

    public function addCardMemory(CardMemory $cardMemory): self
    {
        if (!$this->cardMemories->contains($cardMemory)) {
            $this->cardMemories->add($cardMemory);
            $cardMemory->setCardId($this);
        }

        return $this;
    }

    public function removeCardMemory(CardMemory $cardMemory): self
    {
        if ($this->cardMemories->removeElement($cardMemory)) {
            // set the owning side to null (unless already changed)
            if ($cardMemory->getCardId() === $this) {
                $cardMemory->setCardId(null);
            }
        }

        return $this;
    }
}
