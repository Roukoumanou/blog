<?php

namespace App\Entity;

use App\Entity\Posts;
use App\Entity\Users;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentesRepository;

/**
 * classe entité des commentaires de blogs posts
 * 
 * @ORM\Entity(repositoryClass=CommentesRepository::class)
 * @ORM\Table(name="commentes")
 * 
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 */
class Commentes
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var integer
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Posts", inversedBy="commentes")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $postId;

    /**
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="commentes")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @ORM\Column(type="text", name="content")
     */
    private $content;

    /**
     * @ORM\Column(type="boolean", name="is_valid")
     *
     * @var boolean
     */
    private bool $isValid = false;

    /**
     * @ORM\Column(type="date", name="created_at")
     *
     * @var \DateTimeInterface
     */
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
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
     * @return Posts
     */ 
    public function getPostId(): Posts
    {
        return $this->postId;
    }

    /**
     * @return Users
     */
    public function getUserId(): Users
    {
        return $this->userId;
    }

    /**
     * Set the value of postId
     *
     * @return  self
     */ 
    public function setPostId(Posts $post): self
    {
        $post->addCommentes($this);
        $this->postId = $post;

        return $this;
    }

    /**
     * Set the value of userId
     *
     * @return  self
     */ 
    public function setUserId(Users $user): self
    {
        $user->addCommentes($this);
        $this->userId = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return  self
     */ 
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return  boolean
     */ 
    public function getIsValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @param  boolean  $isValid
     *
     * @return  self
     */ 
    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * @return  \DateTimeInterface
     */ 
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param  \DateTimeInterface  $createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
