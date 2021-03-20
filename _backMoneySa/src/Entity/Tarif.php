<?php

namespace App\Entity;

use App\Repository\TarifRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommissionRepository::class)
 */
class Tarif
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $montantMin;

    /**
     * @ORM\Column(type="integer")
     */
    private $montantMax;

    /**
     * @ORM\Column(type="integer")
     */
    private $fraisEnvoie;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantMin(): ?int
    {
        return $this->montantMin;
    }

    public function setMontantMin(int $montantMin): self
    {
        $this->montantMin = $montantMin;

        return $this;
    }

    public function getMontantMax(): ?int
    {
        return $this->montantMax;
    }

    public function setMontantMax(int $montantMax): self
    {
        $this->montantMax = $montantMax;

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
}
