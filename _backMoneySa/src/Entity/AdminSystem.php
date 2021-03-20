<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AdminSystemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdminSystemRepository::class)
 * @ApiResource ()
 */
class AdminSystem extends User
{

    /**
     * @ORM\OneToMany(targetEntity=Compte::class, mappedBy="adminSystem")
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
            $compte->setAdminSystem($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->compte->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getAdminSystem() === $this) {
                $compte->setAdminSystem(null);
            }
        }

        return $this;
    }
}
