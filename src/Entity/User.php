<?php

namespace App\Entity;

use JsonSerializable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements JsonSerializable, UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'first_name', length: 200)]
    private string $firstName;

    #[ORM\Column(name: 'last_name', length: 200)]
    private string $lastName;

    #[ORM\Column(length: 200, unique: true)]
    private string $email;

    #[ORM\Column(length: 200)]
    private string $password;

    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(length: 20)]
    private ?string $phone = '';

    #[ORM\Column(name: 'phone_prefix', length: 10)]
    private ?string $phonePrefix = '';

    #[ORM\Column(length: 3)]
    private string $country;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhonePrefix(): ?string
    {
        return $this->phonePrefix;
    }

    public function setPhonePrefix(?string $phonePrefix): static
    {
        $this->phonePrefix = $phonePrefix;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function jsonSerialize(): array
    {
        return [
            'id'           => $this->getId(),
            'first_name'    => $this->getFirstName(),
            'last_name'    => $this->getLastName(),
            'full_name'    => $this->getFullName(),
            'email'        => $this->getEmail(),
            'phone'        => $this->getPhone() ? (string) $this->getPhone() : null,
            'phone_prefix'  => $this->getPhonePrefix(),
            'country'      => $this->getCountry(),
            'roles'        => $this->getRoles(),
            'created_at'   => $this->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at'   => $this->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
