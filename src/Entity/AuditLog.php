<?php

namespace App\Entity;

use JsonSerializable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AuditLogRepository;

#[ORM\Entity(repositoryClass: AuditLogRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AuditLog implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $message;

    #[ORM\Column(type: 'json')]
    private array $context = [];

    #[ORM\Column(length: 20)]
    private string $level;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
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

    public function jsonSerialize(): array
    {
        return [
            'id'           => $this->getId(),
            'message'      => $this->getMessage(),
            'context'      => $this->getContext(),
            'level'        => $this->getLevel(),
            'created_at'   => $this->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at'   => $this->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
