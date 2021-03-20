<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AgenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AgenceRepository::class)
 * @ApiResource (
 *      normalizationContext={"groups"={"agences:read"}},
 *     attributes={
 *          "security"="is_granted ('ROLE_AdminSystem') or is_granted ('ROLE_AdminAgence') or is_granted ('ROLE_Caissier') or is_granted ('ROLE_UserAgence')",
 *          "security_message"="Vous n'avez pas access à cette Ressource"
 *     },
 *
 *     collectionOperations={
 *       "get" = {"path"= "/admin/agences/comptes"},
 *
 *          "addAgence"={
 *          "method":"post",
 *          "path":"/admin/agences",
 *           "route_name"="addingAgence",
 *            "security"="is_granted('ROLE_AdminSystem') or is_granted('ROLE_AdminAgence')",
 *            "security_message"="Vous n'avez pas access à cette Ressource"
 *     }
 *
 *     },
 *
 *     itemOperations={
            "get"= {"path"="/admin/agences/{id}"},
 *     "put" = {"path"="/admin/agences/{id}"},
 *     "delete" = {"path"="/admin/agences/{id}"}
 *     }
 * )
 */
class Agence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"agences:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"agences:read"})
     */
    private $nomAgence;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"agences:read"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status=false;

    /**
     * @ORM\OneToOne(targetEntity=Compte::class, cascade={"persist", "remove"})
     * @Groups ({"agences:read"})
     */
    private $compte;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="agence")
     *
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomAgence(): ?string
    {
        return $this->nomAgence;
    }

    public function setNomAgence(string $nomAgence): self
    {
        $this->nomAgence = $nomAgence;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setAgence($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getAgence() === $this) {
                $user->setAgence(null);
            }
        }

        return $this;
    }

}
