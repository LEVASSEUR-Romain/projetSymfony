<?php

namespace App\Entity;

use App\Repository\ListCardRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListCardRepository::class)]
class ListCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'listCards')]
    private ?ListMemory $list_id = null;

    #[ORM\ManyToOne(inversedBy: 'listCards')]
    private ?Cards $card_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getListId(): ?ListMemory
    {
        return $this->list_id;
    }

    public function setListId(?ListMemory $list_id): self
    {
        $this->list_id = $list_id;

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
