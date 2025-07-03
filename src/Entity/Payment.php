<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 255)]
    private string $sender;            // «От кого»

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $requestedAmount;    // Сколько попросил

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $approvedAmount = null; // Сколько одобрил модератор

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $receiptPath = null;   // Путь к файлу квитанции

    #[ORM\ManyToOne(targetEntity: PaymentStatus::class)]
    #[ORM\JoinColumn(nullable: false)]
    private PaymentStatus $status; // pending|approved|rejected


    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRequestedAmount(): ?string
    {
        return $this->requestedAmount;
    }

    public function setRequestedAmount(string $requestedAmount): static
    {
        $this->requestedAmount = $requestedAmount;

        return $this;
    }

    public function getApprovedAmount(): ?string
    {
        return $this->approvedAmount;
    }

    public function setApprovedAmount(?string $approvedAmount): static
    {
        $this->approvedAmount = $approvedAmount;

        return $this;
    }

    public function getReceiptPath(): ?string
    {
        return $this->receiptPath;
    }

    public function setReceiptPath(?string $receiptPath): static
    {
        $this->receiptPath = $receiptPath;

        return $this;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?PaymentStatus
    {
        return $this->status;
    }

    public function setStatus(?PaymentStatus $status): static
    {
        $this->status = $status;

        return $this;
    }
}
