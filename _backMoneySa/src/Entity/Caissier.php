<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CaissierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CaissierRepository::class)
 * @ApiResource(
 *     collectionOperations={
*           "get"= {
 *     "methods" = "GET",
         *          "path" = "/caissiers",
         *          "normalization_context"={"groups"={"caissiers:read"}},
         *     }
 *     }
 *
 * )
 */
class Caissier extends User
{

    /**
     * @ORM\ManyToMany(targetEntity=Compte::class, inversedBy="caissiers")
     */
    private $compte;

    public function __construct()
    {
        $this->compte = new ArrayCollection();
    }

    /**
     * @return Collection|Compte[]
     */
    public function getCompte(): Collection
    {
        return $this->compte;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->compte->contains($compte)) {
            $this->compte[] = $compte;
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        $this->compte->removeElement($compte);

        return $this;
    }
}
