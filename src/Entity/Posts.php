<?php

namespace App\Entity;

use App\Entity\Commentes;
use Doctrine\ORM\Mapping as ORM;
use App\Exception\NotNullException;
use App\Repository\PostsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * classe entité des blogs posts
 * 
 * @ORM\Entity(repositoryClass=PostsRepository::class)
 * @ORM\Table(name="posts")
 * @HasLifecycleCallbacks
 * 
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 */
class Posts
{
    public const PUBLISHED = 20;
    public const DRAFT = 30;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var integer
     */
    private int $id;

    /**
     * @ORM\Column(type="string", name="title", unique=true)
     *
     * @var string
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=200, name="intro")
     *
     * @var string
     */
    private string $intro;

    /**
     * @ORM\Column(type="integer", name="status")
     *
     * @var int
     */
    private int $status = self::DRAFT;

    /**
     * @ORM\Column(type="text", name="content")
     *
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="string", name="created_by")
     *
     * @var string
     */
    private string $createdBy;

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
     * @ORM\OneToMany(targetEntity="Commentes", mappedBy="post_id")
     */
    private $commentes;

    public function __construct()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
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
     * Get the value of title
     *
     * @return  string
     */ 
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param  string  $title
     *
     * @return  self
     */ 
    public function setTitle(string $title): self
    {
        $this->title = $this->notNull(htmlspecialchars($title), "Le champ Titre est Obligatoire!");

        return $this;
    }

    /**
     * Get the value of intro
     *
     * @return  string
     */ 
    public function getIntro(): string
    {
        return $this->intro;
    }

    /**
     * Set the value of intro
     *
     * @param  string  $intro
     *
     * @return  self
     */ 
    public function setIntro(string $intro): self
    {
        $this->intro = $this->notNull(htmlspecialchars($intro), "Le champ Introduction est obligatoire!");

        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  int
     */ 
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  int  $status
     *
     * @return  self
     */ 
    public function setStatus(int $status): self
    {
        if ($status === self::PUBLISHED || $status === self::DRAFT) {
            $this->status = $status;
    
            return $this;
        }
        throw new \Exception("Status inconnu!"); 
    }

    /**
     * Get the value of content
     *
     * @return  string
     */ 
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @param  string  $content
     *
     * @return  self
     */ 
    public function setContent(string $content): self
    {
        $this->content = $this->notNull(htmlspecialchars($content), "Le contenu semble vide");

        return $this;
    }

    /**
     * Get the value of createdBy
     *
     * @return  string
     */ 
    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    /**
     * Set the value of createdBy
     *
     * @param  string  $createdBy
     *
     * @return  self
     */ 
    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = htmlspecialchars($createdBy);

        return $this;
    }

    /**
     * Get the value of updatedAt
     *
     * @return  \DateTimeInterface
     */ 
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param  \DateTimeInterface  $updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of createdAt
     *
     * @return  \DateTimeInterface
     */ 
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param  \DateTimeInterface  $createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param Commentes $commente
     * @return self
     */
    public function addCommentes(Commentes $commente): self
    {
        if (!$this->commentes->contains($commente)) {
        $this->commentes[] = $commente;
        $commente->setPostId($this);
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

    private function notNull(string $champ, string $message)
    {
        if (empty($champ)) {
            throw new NotNullException($message);
        }

        return $champ;
    }
}
