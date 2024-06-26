<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;// importer la classe PasswordAuthenticatedUserInterface
use Symfony\Component\Security\Core\User\UserInterface;// importer la classe UserInterface
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]// annotation pour définir l'entité User
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]// annotation pour définir un email unique
class User implements UserInterface, PasswordAuthenticatedUserInterface// implémenter les interfaces UserInterface et PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(// validation de l'email grâce à la classe Assert qui permet de définir des contraintes
        message: 'The email "{{ value }}" is not a valid email. Try again.',
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(
        message: 'Please enter a password',
    )]
    // Put this validatioon constrain in the registration form
    // #[Assert\Regex(
    //     pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
    //     message: 'Your password must contain : at least 1 uppercase letter, 1 lowercase letter, 1 number, at least 1 special character, at least 8 characters'
    // )]
    private ?string $password = null;

    #[Assert\Length(// validation de la longueur du prénom
        min: 2,
        max: 50,
        minMessage: 'Your firstname must be at least {{ limit }} characters long',
        maxMessage: 'Your firstname cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $firstname = null;
    
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Your lastname must be at least {{ limit }} characters long',
        maxMessage: 'Your lastname cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $lastname = null;

    #[Assert\Range(
        min: 1940,
        max: 2007,
        notInRangeMessage: 'Your birthyear must be between {{ min }} and {{ max }}',
    )]
    #[ORM\Column(nullable: true)]
    private ?int $birthyear = null;

    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Your address must be at least {{ limit }} characters long',
        maxMessage: 'Your address cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Your city must be at least {{ limit }} characters long',
        maxMessage: 'Your city cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $city = null;

    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Your country must be at least {{ limit }} characters long',
        maxMessage: 'Your country cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = 'default.png';

    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Your job must be at least {{ limit }} characters long',
        maxMessage: 'Your job cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $job = null;

    #[ORM\OneToMany(mappedBy: 'host', targetEntity: Room::class, orphanRemoval: true)]
    private Collection $rooms;

    #[ORM\OneToMany(mappedBy: 'traveler', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'traveler', targetEntity: Booking::class, orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'traveler', targetEntity: Favorite::class, orphanRemoval: true)]
    private Collection $favorites;

    public function __construct()// constructeur de la classe User
    {
        $this->rooms = new ArrayCollection();// initialiser la collection de rooms
        $this->reviews = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    // Fusion : Fistname + Lastname
    public function getFullname(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    // Current year minus birthyear to get age
    public function getAge(): ?int
    {
        return date('Y') - $this->birthyear;
    }
    
    public function getBirthyear(): ?int
    {
        return $this->birthyear;
    }

    public function setBirthyear(?int $birthyear): static
    {
        $this->birthyear = $birthyear;

        return $this;
    }


    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): static
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): static
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            $room->setHost($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getHost() === $this) {
                $room->setHost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setTraveler($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getTraveler() === $this) {
                $review->setTraveler(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setTraveler($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getTraveler() === $this) {
                $booking->setTraveler(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setTraveler($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getTraveler() === $this) {
                $favorite->setTraveler(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getFullname();
    }

    public function getFullAddress(): string
    {
        return $this->address . ', ' . $this->city . ', ' . $this->country;
    }
}
