<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * @ApiResource (
 *      normalizationContext={"groups"={"transactions:read"}},
 *     attributes={
 *          "security"="is_granted ('ROLE_AdminSystem') or is_granted ('ROLE_AdminAgence') or is_granted ('ROLE_Caissier') or is_granted ('ROLE_UserAgence')",
 *          "security_message"="Vous n'avez pas access à cette Ressource"
 *     },
 *     collectionOperations={
            "get"= {"path"= "/admin/transactions"},
 *          "addTransaction" = {
 *               "path" = "/admin/transactions",
 *              "method" = "POST",
 *              "route_name" = "post_transaction"
 *      },
 *      "findTransaction" = {
 *               "path" = "/admin/transactions/code",
 *              "method" = "POST",
 *              "route_name" = "find_transaction"
 *      },
 *     "calculFrais" = {
 *               "path" = "/admin/transactions/calculer",
 *              "method" = "POST",
 *              "route_name" = "post_calculer"
 *      },
 *     "deCalculFrais" = {
 *               "path" = "/admin/transactions/decalculer",
 *              "method" = "POST",
 *              "route_name" = "post_decalculer"
 *      }
 *     },
 *
 *     itemOperations={
            "get"= {"path"= "/admin/transactions/{id}"},
 *          "put"= {"path"= "/admin/transactions/{id}"},
 *          "deleteTransaction" = {
 *              "path" = "/admin/transactions/delete",
 *              "method" = "DELETE",
 *              "route_name" = "delete_transaction"
 *          }
 *     }
 * )
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"transactions:read"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"transactions:read"})
     */
    private ?int $montant;

    /**
     * @ORM\Column(type="date")
     * @Groups ({"transactions:read"})
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?\DateTimeInterface $dateRetrait;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?\DateTimeInterface $dateAnnulation;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"transactions:read"})
     */
    private $TTC;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"transactions:read"})
     */
    private  ?int $fraisEtat;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"transactions:read"})
     */
    private ?int $fraisSystem;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"transactions:read"})
     */
    private ?int $fraisEnvoie;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"transactions:read"})
     */
    private ?int $fraisRetrait;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"transactions:read"})
     */
    private ?int $codeTransaction;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="transaction")
     * @Groups ({"transactions:read"})
     */
    private ?Compte $compte;




    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @Groups ({"transactions:read"})
     */
    private ?User $userDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @Groups ({"transactions:read"})
     */
    private $userRetrait;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     * @Groups ({"transactions:read"})
     */
    private $clientEnvoi;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     * @Groups ({"transactions:read"})
     */
    private $clientRetrait;



    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getDateAnnulation(): ?\DateTimeInterface
    {
        return $this->dateAnnulation;
    }

    public function setDateAnnulation(\DateTimeInterface $dateAnnulation): self
    {
        $this->dateAnnulation = $dateAnnulation;

        return $this;
    }

    public function getTTC(): ?int
    {
        return $this->TTC;
    }

    public function setTTC(int $TTC): self
    {
        $this->TTC = $TTC;

        return $this;
    }

    public function getFraisEtat(): ?int
    {
        return $this->fraisEtat;
    }

    public function setFraisEtat(int $fraisEtat): self
    {
        $this->fraisEtat = $fraisEtat;

        return $this;
    }

    public function getFraisSystem(): ?int
    {
        return $this->fraisSystem;
    }

    public function setFraisSystem(int $fraisSystem): self
    {
        $this->fraisSystem = $fraisSystem;

        return $this;
    }

    public function getFraisEnvoie(): ?int
    {
        return $this->fraisEnvoie;
    }

    public function setFraisEnvoie(int $fraisEnvoie): self
    {
        $this->fraisEnvoie = $fraisEnvoie;

        return $this;
    }

    public function getFraisRetrait(): ?int
    {
        return $this->fraisRetrait;
    }

    public function setFraisRetrait(int $fraisRetrait): self
    {
        $this->fraisRetrait = $fraisRetrait;

        return $this;
    }

    public function getCodeTransaction(): ?string
    {
        return $this->codeTransaction;
    }

    public function setCodeTransaction(string $codeTransaction): self
    {
        $this->codeTransaction = $codeTransaction;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

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

    public function getUserDepot(): ?User
    {
        return $this->userDepot;
    }

    public function setUserDepot(?User $userDepot): self
    {
        $this->userDepot = $userDepot;

        return $this;
    }

    public function getUserRetrait(): ?User
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?User $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }

    public function getClientEnvoi(): ?Client
    {
        return $this->clientEnvoi;
    }

    public function setClientEnvoi(?Client $clientEnvoi): self
    {
        $this->clientEnvoi = $clientEnvoi;

        return $this;
    }

    public function getClientRetrait(): ?Client
    {
        return $this->clientRetrait;
    }

    public function setClientRetrait(?Client $clientRetrait): self
    {
        $this->clientRetrait = $clientRetrait;

        return $this;
    }



}
