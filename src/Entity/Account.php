<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $balance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(length: 255)]
    private ?string $nameAccount = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private ?User $userID = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AccountType $accountType = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    private ?Currency $currency = null;

    #[ORM\OneToOne(inversedBy: 'account', cascade: ['persist', 'remove'])]
    private ?Goal $goal = null;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Debt::class, orphanRemoval: true)]
    private Collection $debts;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Income::class, orphanRemoval: true)]
    private Collection $incomes;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Expense::class, orphanRemoval: true)]
    private Collection $expenses;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: History::class, orphanRemoval: true)]
    private Collection $histories;

    public function __construct()
    {
        $this->debts = new ArrayCollection();
        $this->incomes = new ArrayCollection();
        $this->expenses = new ArrayCollection();
        $this->histories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getNameAccount(): ?string
    {
        return $this->nameAccount;
    }

    public function setNameAccount(string $nameAccount): static
    {
        $this->nameAccount = $nameAccount;

        return $this;
    }

    public function getUserID(): ?User
    {
        return $this->userID;
    }

    public function setUserID(?User $userID): static
    {
        $this->userID = $userID;

        return $this;
    }

    public function getAccountType(): ?AccountType
    {
        return $this->accountType;
    }

    public function setAccountType(?AccountType $accountType): static
    {
        $this->accountType = $accountType;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getGoal(): ?Goal
    {
        return $this->goal;
    }

    public function setGoal(?Goal $goal): static
    {
        $this->goal = $goal;

        return $this;
    }

    /**
     * @return Collection<int, Debt>
     */
    public function getDebts(): Collection
    {
        return $this->debts;
    }

    public function addDebt(Debt $debt): static
    {
        if (!$this->debts->contains($debt)) {
            $this->debts->add($debt);
            $debt->setAccount($this);
        }

        return $this;
    }

    public function removeDebt(Debt $debt): static
    {
        if ($this->debts->removeElement($debt)) {
            // set the owning side to null (unless already changed)
            if ($debt->getAccount() === $this) {
                $debt->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Income>
     */
    public function getIncomes(): Collection
    {
        return $this->incomes;
    }

    public function addIncome(Income $income): static
    {
        if (!$this->incomes->contains($income)) {
            $this->incomes->add($income);
            $income->setAccount($this);
        }

        return $this;
    }

    public function removeIncome(Income $income): static
    {
        if ($this->incomes->removeElement($income)) {
            // set the owning side to null (unless already changed)
            if ($income->getAccount() === $this) {
                $income->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): static
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setAccount($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): static
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getAccount() === $this) {
                $expense->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, History>
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): static
    {
        if (!$this->histories->contains($history)) {
            $this->histories->add($history);
            $history->setAccount($this);
        }

        return $this;
    }

    public function removeHistory(History $history): static
    {
        if ($this->histories->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getAccount() === $this) {
                $history->setAccount(null);
            }
        }

        return $this;
    }
}
