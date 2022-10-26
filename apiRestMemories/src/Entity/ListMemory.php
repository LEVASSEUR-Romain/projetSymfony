<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ListMemoryRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ListMemoryRepository::class)]
class ListMemory
{
    const MIN_LENGTH_NAME = 2;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'listMemories')]
    private ?User $user_id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: self::MIN_LENGTH_NAME,
        minMessage: 'La taille du nom doit etre supérieur à {{ limit }}',
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'list_id', targetEntity: ListCard::class)]
    private Collection $listCards;

    public function __construct()
    {
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
            $listCard->setListId($this);
        }

        return $this;
    }

    public function removeListCard(ListCard $listCard): self
    {
        if ($this->listCards->removeElement($listCard)) {
            // set the owning side to null (unless already changed)
            if ($listCard->getListId() === $this) {
                $listCard->setListId(null);
            }
        }

        return $this;
    }
}
