<?php

namespace App\Entity;

use App\Repository\TrajetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrajetRepository::class)]
class Trajet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $villeDepart = null;

    #[ORM\Column(length: 255)]
    private ?string $villeArrivee = null;

    #[ORM\Column]
    private ?\DateTime $dateHeureDepart = null;

    #[ORM\Column]
    private ?\DateTime $dateHeureArrivee = null;

    #[ORM\Column]
    private ?int $placesDisponibles = null;

    #[ORM\Column]
    private ?int $credits = null;

    #[ORM\Column]
    private ?bool $energieElectrique = null;

    #[ORM\ManyToOne(inversedBy: 'trajets')]
    private ?User $conducteur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVilleDepart(): ?string
    {
        return $this->villeDepart;
    }

    public function setVilleDepart(string $villeDepart): static
    {
        $this->villeDepart = $villeDepart;

        return $this;
    }

    public function getVilleArrivee(): ?string
    {
        return $this->villeArrivee;
    }

    public function setVilleArrivee(string $villeArrivee): static
    {
        $this->villeArrivee = $villeArrivee;

        return $this;
    }

    public function getDateHeureDepart(): ?\DateTime
    {
        return $this->dateHeureDepart;
    }

    public function setDateHeureDepart(\DateTime $dateHeureDepart): static
    {
        $this->dateHeureDepart = $dateHeureDepart;

        return $this;
    }

    public function getDateHeureArrivee(): ?\DateTime
    {
        return $this->dateHeureArrivee;
    }

    public function setDateHeureArrivee(\DateTime $dateHeureArrivee): static
    {
        $this->dateHeureArrivee = $dateHeureArrivee;

        return $this;
    }

    public function getPlacesDisponibles(): ?int
    {
        return $this->placesDisponibles;
    }

    public function setPlacesDisponibles(int $placesDisponibles): static
    {
        $this->placesDisponibles = $placesDisponibles;

        return $this;
    }

    public function getCredits(): ?int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): static
    {
        $this->credits = $credits;

        return $this;
    }

    public function isEnergieElectrique(): ?bool
    {
        return $this->energieElectrique;
    }

    public function setEnergieElectrique(bool $energieElectrique): static
    {
        $this->energieElectrique = $energieElectrique;

        return $this;
    }

    public function getConducteur(): ?User
    {
        return $this->conducteur;
    }

    public function setConducteur(?User $conducteur): static
    {
        $this->conducteur = $conducteur;

        return $this;
    }
}
