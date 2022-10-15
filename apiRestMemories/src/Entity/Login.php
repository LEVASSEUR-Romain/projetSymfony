<?php

namespace App\Entity;

use App\Repository\LoginRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: LoginRepository::class)]
#[UniqueEntity('mail', message: "cette email a deja été enregistré")]
#[UniqueEntity('pseudo', message: "ce pseudo est deja utilisé")]
class Login
{
    // variable constraint
    const MAX_LENGTH_PSEUDO = 25;
    const MIN_LENGTH_PSEUDO = 5;
    const MAX_LENGTH_PASS = 25;
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

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: self::MIN_LENGTH_PASS,
        max: self::MAX_LENGTH_PASS,
        minMessage: 'La taille du mot de passe doit etre supérieur à {{ limit }}',
        maxMessage: 'La taille du mot de passe doit etre inférieur à {{ limit }}',
    )]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email(
        message: "le mail : {{ value }} n'est pas un mail valide.",
    )]
    private ?string $mail = null;

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

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }
}
