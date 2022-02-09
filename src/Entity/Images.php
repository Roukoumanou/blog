<?php

namespace App\Entity;

/**
 * @author Amidou Roukoumanou <roukoumanouamidou@gmail.com>
 */
class Images
{
    /**
     * @var integer
     */
    private int $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var \DateTimeInterface
     */
    private \DateTimeInterface $createdAt;

    /**
     * @var \DateTimeInterface
     */
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
    }



    /**
     * Get the value of id
     * @return int
     */ 
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of name
     * @return string
     */ 
    public function getName(): string
    {
        return $this->name;
    }

   /**
    * @param string $name
    * @return self
    */ 
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of createdAt
     * @return \DateTimeInterface
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
     * @return \DateTimeInterface
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
    public function setUpdatedAt(?\DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
