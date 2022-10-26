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
    const MIN_LENGTH = 1;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    private ?User $user_id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: self::MIN_LENGTH,
        minMessage: 'La taille du devant doit etre supérieur à {{ limit }}',
    )]
    private ?string $front = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: self::MIN_LENGTH,
        minMessage: 'La taille du derrière doit etre supérieur à {{ limit }}',
    )]
    private ?string $back = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $perso_front = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $perso_back = null;

    #[ORM\OneToMany(mappedBy: 'card_id', targetEntity: ListCard::class)]
    private Collection $listCards;


    public function __construct()
    {
        $this->cardMemories = new ArrayCollection();
        $this->listCards = new ArrayCollection();
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
     * @return Collection<int, ListCard>
     */
    public function getListCards(): Collection
    {
        return $this->listCards;
    }

    public function addListCard(ListCard $listCard): self
    {
        if (!$this->listCards->contains($listCard)) {
            $this->listCards->add($listCard);
            $listCard->setCardId($this);
        }

        return $this;
    }

    public function removeListCard(ListCard $listCard): self
    {
        if ($this->listCards->removeElement($listCard)) {
            // set the owning side to null (unless already changed)
            if ($listCard->getCardId() === $this) {
                $listCard->setCardId(null);
            }
        }

        return $this;
    }
}
