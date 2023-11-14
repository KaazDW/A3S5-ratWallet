<?php

namespace App\Entity;

use App\Repository\DebtRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DebtRepository::class)]
class Debt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $debtAmount = null;

    #[ORM\Column(length: 255)]
    private ?string $creditor = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadline = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebtAmount(): ?float
    {
        return $this->debtAmount;
    }

    public function setDebtAmount(float $debtAmount): static
    {
        $this->debtAmount = $debtAmount;

        return $this;
    }

    public function getCreditor(): ?string
    {
        return $this->creditor;
    }

    public function setCreditor(string $creditor): static
    {
        $this->creditor = $creditor;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }
}
