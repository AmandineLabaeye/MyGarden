<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticlesRepository")
 */
class Articles
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categories;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $namelatin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $toxicite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $environnement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $urlBuy;

    /**
     * @ORM\Column(type="integer")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="articles", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $date;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getCategories(): ?Category
    {
        return $this->categories;
    }

    public function setCategories(?Category $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getNamelatin(): ?string
    {
        return $this->namelatin;
    }

    public function setNamelatin(?string $namelatin): self
    {
        $this->namelatin = $namelatin;

        return $this;
    }

    public function getToxicite(): ?string
    {
        return $this->toxicite;
    }

    public function setToxicite(?string $toxicite): self
    {
        $this->toxicite = $toxicite;

        return $this;
    }

    public function getEnvironnement(): ?string
    {
        return $this->environnement;
    }

    public function setEnvironnement(?string $environnement): self
    {
        $this->environnement = $environnement;

        return $this;
    }

    public function getUrlBuy(): ?string
    {
        return $this->urlBuy;
    }

    public function setUrlBuy(?string $urlBuy): self
    {
        $this->urlBuy = $urlBuy;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticles($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticles() === $this) {
                $comment->setArticles(null);
            }
        }

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }
}
