<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


/**
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={"archive":"partial"},
 * )
 *
 * @ApiResource (
 *    denormalizationContext={"groups"={"comptes:write"}},
 *     normalizationContext={"groups"={"compts:read"}},
 *     attributes={
 *          "security"="is_granted ('ROLE_AdminSystem') or is_granted ('ROLE_AdminAgence') or is_granted ('ROLE_Caisser') or is_granted ('ROLE_UserAgence')",
 *          "security_message"="Vous n'avez pas access à cette Ressource"
 *     },
 *
 *     routePrefix="/admin",
 *     collectionOperations={
*           "get", "post","getSolde":{"method":"GET","path":"/comptes/solde"}
 *     },
 *     itemOperations={
 *         "get", "put", "delete",
 *     }
 * )
 */
class Compte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"comptes:write", "compts:read", "agences:read", "transactions:read", "depots:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"comptes:write", "compts:read", "agences:read", "transactions:read", "depots:read"})
     */
    private $numCompte;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"comptes:write", "compts:read", "agences:read", "transactions:read", "depots:read"})
     */
    private $solde;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="compte")
     * @Groups ({"comptes:write", "compts:read"})
     */
    private $transaction;

    /**
     * @ORM\ManyToMany(targetEntity=Caissier::class, mappedBy="compte")
     * @Groups ({"comptes:write", "compts:read"})
     */
    private $caissiers;

    /**
     * @ORM\ManyToOne(targetEntity=AdminSystem::class, inversedBy="compte")
     * @Groups ({"comptes:write", "compts:read"})
     */
    private $adminSystem;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status=false;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="compte")
     */
    private $depots;

    public function __construct()
    {
        $this->transaction = new ArrayCollection();
        $this->caissiers = new ArrayCollection();
        $this->depots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCompte(): ?string
    {
        return $this->numCompte;
    }

    public function setNumCompte(string $numCompte): self
    {
        $this->numCompte = $numCompte;

        return $this;
    }

    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): self
    {
        $this->solde = $solde;

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
            $transaction->setCompte($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transaction->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCompte() === $this) {
                $transaction->setCompte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Caissier[]
     */
    public function getCaissiers(): Collection
    {
        return $this->caissiers;
    }

    public function addCaissier(Caissier $caissier): self
    {
        if (!$this->caissiers->contains($caissier)) {
            $this->caissiers[] = $caissier;
            $caissier->addCompte($this);
        }

        return $this;
    }

    public function removeCaissier(Caissier $caissier): self
    {
        if ($this->caissiers->removeElement($caissier)) {
            $caissier->removeCompte($this);
        }

        return $this;
    }

    public function getAdminSystem(): ?AdminSystem
    {
        return $this->adminSystem;
    }

    public function setAdminSystem(?AdminSystem $adminSystem): self
    {
        $this->adminSystem = $adminSystem;

        return $this;
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

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

        return $this;
    }

}
