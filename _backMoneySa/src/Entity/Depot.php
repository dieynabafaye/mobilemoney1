<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\DepotRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=DepotRepository::class)
 * normalizationContext={"groups"={"depots:read"}},
 * attributes={
 *          "security"="is_granted ('ROLE_AdminSystem') or is_granted ('ROLE_Caissier')",
 *          "security_message"="Vous n'avez pas access à cette Ressource"
 *     },
 * @ApiResource(
 *     itemOperations={
 *        "GET"= {"path"= "/admin/depots/{id}"},
 *         "PUT"= {"path"= "/admin/depots/{id}"},
 *         "deleteDepot"={
 *              "method":"DELETE",
 *              "path": "/admin/depots/{id}",
 *              "route_name":"deleteDepot"
 *          }
 *     },
 *     collectionOperations={
 *          "GET" = {"path"= "/admin/depots"},
 *          "addDepot"={
 *          "method":"POST",
 *          "path": "/admin/depots",
 *          "route_name":"addDepot"
 *     }
 *     }
 * )
 */
class Depot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"depots:read"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="integer")
     * @Groups ({"depots:read", "transactions:read"})
     */
    private ?int $montant;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="depots")
     * @Groups ({"depots:read"})
     */
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="depots")
     * @Groups ({"depots:read"})
     */
    private ?Compte $compte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
}
