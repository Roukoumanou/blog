<?php

namespace App\Entity;

use App\Entity\Posts;
use App\Entity\Users;
use Doctrine\ORM\Mapping as ORM;
use App\Exception\NotNullException;

/**
 * classe entité des commentaires de blogs posts
 * 
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 */
class Commentes
{
    /**
     * @var integer
     */
    private int $id;

    private int $postId;

    private int $userId;

    /**
     * @var string
     */
    private $content;

    /**
     * @var boolean
     */
    private bool $isValid = false;

    /**
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
     * @return int
     */ 
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $post
     * @return self
     */ 
    public function setPostId(int $post): self
    {
        $this->postId = $post;

        return $this;
    }

    /**
     * @param int $user
     * @return self
     */
    public function setUserId(int $user): self
    {
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
        $this->content = $this->notNull(htmlspecialchars($content), "Le champs Message est obligatoire");

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

    private function notNull(string $champ, string $message)
    {
        if (empty($champ)) {
            throw new NotNullException($message);
        }

        return $champ;
    }
}
