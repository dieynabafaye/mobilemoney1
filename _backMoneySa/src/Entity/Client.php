<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @ApiResource ()
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"transactions:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"transactions:read"})
     */
    private $nomComplet;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"transactions:read"})
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups ({"transactions:read"})
     */
    private $CNI;

    /**
     * @ORM\ManyToMany(targetEntity=Transaction::class, inversedBy="clients")
     *
     */
    private $transaction;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="clientEnvoi")
     */
    private $transactions;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;


    public function __construct()
    {
        $this->transaction = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getCNI(): ?string
    {
        return $this->CNI;
    }

    public function setCNI(string $CNI): self
    {
        $this->CNI = $CNI;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransaction(): Collection
    {
        return $this->transaction;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transaction->contains($transaction)) {
            $this->transaction[] = $transaction;
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        $this->transaction->removeElement($transaction);

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

}
