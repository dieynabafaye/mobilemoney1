<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *  @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type_id", type="integer")
 * @ORM\DiscriminatorMap({1 ="AdminSystem",2="Caissier",3="AdminAgence",4="UserAgence", 5="User"})
 * * @UniqueEntity(
 * fields={"username"},
 * message="Le nom d'utilisateur doit être unique"
 * )
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"users:read"}},
 *     attributes={
 *          "security"="is_granted ('ROLE_AdminSystem')",
 *          "security_message"="Vous n'avez pas access à cette Ressource"
 *     },
 *
 *     collectionOperations={
 *       "get"={"path"="/admin/users"},
 *       "post"={"path"="/admin/users"},
 *
 *     },
 *
 *     itemOperations={
 *         "get_user"={
 *         "method"="GET", "path":"/admin/users/{id}",
 *     },
 *
 *      "putUserId":{
 *           "method":"put",
 *          "path":"/admin/users/{id}",
 *
 *          },
 *     },
 * )
 *
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"users:read", "caissiers:read"})
     * @Groups ({"comptes:write", "compts:read", "transactions:read", "depots:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank (message="le nom d'utilisateur est obligatoire")
     * @Groups ({"users:read", "caissiers:read", "comptes:write", "compts:read"})
     */
    private $username;


    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank (message="le mot de passe est obligatoire")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank (message="le nom est obligatoire")
     * @Groups ({"users:read", "caissiers:read", "comptes:write", "compts:read", "transactions:read", "depots:read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank (message="le prenom est obligatoire")
     * @Groups ({"users:read", "comptes:write", "compts:read", "caissiers:read", "transactions:read", "depots:read"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank (message="le numéro de téléphone est obligatoire")
     * @Groups ({"users:read", "comptes:write", "compts:read", "caissiers:read", "depots:read"})
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank (message="CNI obligatoire")
     * @Groups ({"users:read", "comptes:write", "compts:read", "caissiers:read"})
     */
    private $CNI;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank (message="l'adresse est obligatoire")
     * @Groups ({"users:read", "comptes:write", "compts:read", "caissiers:read", "transactions:read"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status=false;

    /**
     * @ORM\Column(type="blob")
     */
    private $avatar;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="users")
     * @Groups ({"users:read"})
     */
    private $profil;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="userDepot")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="user")
     */
    private $depots;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="users")
     */
    private $agence;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->depots = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_'.$this->profil->getLibelle();

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

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

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setUserDepot($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUserDepot() === $this) {
                $transaction->setUserDepot(null);
            }
        }

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
            $depot->setUser($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getUser() === $this) {
                $depot->setUser(null);
            }
        }

        return $this;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

        return $this;
    }

}
