<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('mail', message: "cette email a deja été enregistré")]
#[UniqueEntity('pseudo', message: "ce pseudo est deja utilisé")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // variable constraint
    const MAX_LENGTH_PSEUDO = 25;
    const MIN_LENGTH_PSEUDO = 5;
    const MIN_LENGTH_PASS = 4;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: self::MIN_LENGTH_PSEUDO,
        max: self::MAX_LENGTH_PSEUDO,
        minMessage: 'La taille du pseudo doit etre supérieur à {{ limit }}',
        maxMessage: 'La taille du pseudo doit etre inférieur à {{ limit }}',
    )]
    #[Assert\Regex('/^[a-zA-Z0-9]+$/', message: "le pseudo contient de caractére spéciaux")]
    private ?string $pseudo = null;


    #[Assert\NotBlank]
    #[Assert\Length(
        min: self::MIN_LENGTH_PASS,
        minMessage: 'La taille du mot de passe doit etre supérieur à {{ limit }}',
    )]
    private ?string $plainPassword = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mail = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Cards::class)]
    private Collection $cards;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: ListMemory::class)]
    private Collection $listMemories;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
        $this->listMemories = new ArrayCollection();
    }


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string|null $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function setPlainPassword(string $plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Cards>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Cards $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
            $card->setUserId($this);
        }

        return $this;
    }

    public function removeCard(Cards $card): self
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getUserId() === $this) {
                $card->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ListMemory>
     */
    public function getListMemories(): Collection
    {
        return $this->listMemories;
    }

    public function addListMemory(ListMemory $listMemory): self
    {
        if (!$this->listMemories->contains($listMemory)) {
            $this->listMemories->add($listMemory);
            $listMemory->setUserId($this);
        }

        return $this;
    }

    public function removeListMemory(ListMemory $listMemory): self
    {
        if ($this->listMemories->removeElement($listMemory)) {
            // set the owning side to null (unless already changed)
            if ($listMemory->getUserId() === $this) {
                $listMemory->setUserId(null);
            }
        }

        return $this;
    }
}
