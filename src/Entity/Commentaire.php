<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min: 50, minMessage: "Votre commentaire doit faire minimum 50 caractères !")]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_comment = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Vehicule $vehicule = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?User $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDateComment(): ?\DateTimeImmutable
    {
        return $this->date_comment;
    }

    public function setDateComment(\DateTimeImmutable $date_comment): static
    {
        $this->date_comment = $date_comment;

        return $this;
    }

    public function getVehicule(): ?Vehicule
    {
        return $this->vehicule;
    }

    public function setVehicule(?Vehicule $vehicule): static
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }
}
