<?php

namespace App\Entity;

use App\Repository\BookingRepository;// importer la classe BookingRepository
use Doctrine\DBAL\Types\Types;// importer la classe Types
use Doctrine\ORM\Mapping as ORM;// importer la classe ORM

#[ORM\Entity(repositoryClass: BookingRepository::class)]// annotation pour définir l'entité Booking
class Booking
{
    #[ORM\Id]// annotation pour définir l'identifiant
    #[ORM\GeneratedValue]// annotation pour générer automatiquement l'identifiant
    #[ORM\Column]// annotation pour définir la colonne
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $number = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $check_in = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $check_out = null;

    #[ORM\Column]
    private ?int $occupants = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]// annotation pour définir la relation entre Booking et User
    #[ORM\JoinColumn(nullable: false)]// annotation pour définir la colonne de jointure
    private ?User $traveler = null;// définir l'utilisateur qui a effectué la réservation

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Review $review = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    #[ORM\Column]
    private ?bool $isPaid = false;

    #[ORM\Column]
    private ?bool $isConfirmed = false;

    #[ORM\OneToOne(mappedBy: 'booking', cascade: ['persist', 'remove'])]
    private ?Invoice $invoice = null;

    public function __construct()// constructeur de la classe Booking
    {
        $this->number = 'BNB-' . random_int(1000, 9999); // génère un numéro de réservation
        $this->created_at = new \DateTime(); // date de création de la réservation
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string// méthode pour obtenir le numéro de réservation
    {
        return $this->number;
    }

    public function setNumber(string $number): static// méthode pour définir le numéro de réservation
    {
        $this->number = $number;

        return $this;
    }

    public function getCheckIn(): ?\DateTimeInterface
    {
        return $this->check_in;
    }

    public function setCheckIn(\DateTimeInterface $check_in): static
    {
        $this->check_in = $check_in;

        return $this;
    }

    public function getCheckOut(): ?\DateTimeInterface
    {
        return $this->check_out;
    }

    public function setCheckOut(\DateTimeInterface $check_out): static
    {
        $this->check_out = $check_out;

        return $this;
    }

    public function getOccupants(): ?int
    {
        return $this->occupants;
    }

    public function setOccupants(int $occupants): static
    {
        $this->occupants = $occupants;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getTraveler(): ?User
    {
        return $this->traveler;
    }

    public function setTraveler(?User $traveler): static
    {
        $this->traveler = $traveler;

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): static
    {
        $this->review = $review;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    // Convertir la date de checkin en chaîne
    public function getCheckInString(): string
    {
        return $this->getCheckIn()->format('d/m/Y');
    }

    // Convertir la date de checkout en chaîne
    public function getCheckOutString(): string
    {
        return $this->getCheckOut()->format('d/m/Y');
    }

    // Obtenir le nombre de jours de réservation
    public function getDays(): int
    {
        $diff = $this->getCheckIn()->diff($this->getCheckOut());
        return $diff->days;
    }

    public function isIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): static
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function isIsConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): static
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): static
    {
        // modifier l'entité associée, si nécessaire
        if ($invoice->getBooking() !== $this) {
            $invoice->setBooking($this);
        }

        $this->invoice = $invoice;// définir la facture

        return $this;
    }

}
