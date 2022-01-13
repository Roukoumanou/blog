<?php 

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Commentes;
use Zend\Crypt\Password\Bcrypt;
use Doctrine\ORM\Mapping as ORM;
use App\Exception\NotNullException;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @ORM\Table(name="users")
 */
class Users
{
    public const ROLE_ADMIN = 10;
    public const ROLE_USER  = 11;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var integer
     */
    private int $id;

    /**
     * @ORM\Column(type="string", name="first_name")
     *
     * @var string
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", name="last_name")
     *
     * @var string
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    private string $email;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private string $password;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private int $role = self::ROLE_USER;

    /**
     * @ORM\Column(type="boolean", name="is_valid")
     *
     * @var bool
     */
    private $isValid = false;

    /**
     * @ORM\Column(type="date", name="created_at")
     *
     * @var \DateTimeInterface
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="date", name="updated_at", nullable=true)
     *
     * @var \DateTimeInterface
     */
    private \DateTimeInterface $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="Images", cascade={"persist", "remove", "merge"})
     * @ORM\JoinColumn(name="images_id", referencedColumnName="id", nullable=true)
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="Commentes", mappedBy="userId")
     */
    private $commentes;

    public function __construct()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }else{
            $this->updatedAt = new \DateTime();
        }

        $this->commentes = new ArrayCollection();
    }

    /**
     * Get the value of id
     *
     * @return  integer
     */ 
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of firstName
     */ 
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @return  self
     */ 
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $this->notNull(htmlspecialchars($firstName), "Le champs nom est obligatoire!");

        return $this;
    }

    /**
     * Get the value of lastName
     */ 
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @return  self
     */ 
    public function setLastName(string $lastName): self
    {
        $this->notNull($lastName, "Le champs prénom est obligatoire!");
        $this->lastName = htmlspecialchars($lastName);

        return $this;
    }

    private function notNull(string $champ, string $message)
    {
        if (empty($champ)) {
            throw new NotNullException($message);
        }

        return $champ;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail(string $email): self
    {
        $this->notNull($email, "Le champs email est obligatoire!");
        if(! filter_var(htmlspecialchars($email), FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Cet Mail n'est pas valide");
        }
        $this->email = htmlspecialchars($email);

        return $this;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword(string $password): self
    {
        $this->notNull($password, "Le mot de passe est obligatoire!");

        $bcrypt = new Bcrypt();
        $hash = $bcrypt->create(htmlspecialchars($password)); // password hashed

        $this->password = $hash;

        return $this;
    }

    /**
     * Get the value of role
     */ 
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * Set the value of role
     *
     * @return  self
     */ 
    public function setRole(int $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the value of is_valid
     */ 
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Set the value of is_valid
     *
     * @return  self
     */ 
    public function setIsValid($is_valid): self
    {
        $this->isValid = $is_valid;

        return $this;
    }

    /**
     * Get the value of createdAt
     */ 
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */ 
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of images
     */ 
    public function getImages(): ?Images
    {
        return $this->images;
    }

    /**
     * Set the value of images
     *
     * @return  self
     */ 
    public function setImages(?Images $images): self
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @param Commentes $commente
     * @return self
     */
    public function addCommentes(Commentes $commente): self
    {
        if (! $this->commentes->contains($commente)) {
            $this->commentes[] = $commente;
            $commente->setUserId($this);
        }

        return $this;
    }

    public function removeCommente(Commentes $commente): self
    {
        $this->commentes->removeElement($commente);

        return $this;
    }

    /**
     * @return Collection|Commentes
     */
    public function getCommentes(): Collection
    {
        return $this->commentes;
    }
}
