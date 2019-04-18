<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PublicationsProfilRepository")
 */
class PublicationsProfil
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="publicationsProfils")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $publication;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommentsPublication", mappedBy="publication", orphanRemoval=true)
     */
    private $commentsPublications;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $page;

    public function __construct()
    {
        $this->commentsPublications = new ArrayCollection();
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

    public function getPublication(): ?string
    {
        return $this->publication;
    }

    public function setPublication(string $publication): self
    {
        $this->publication = $publication;

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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection|CommentsPublication[]
     */
    public function getCommentsPublications(): Collection
    {
        return $this->commentsPublications;
    }

    public function addCommentsPublication(CommentsPublication $commentsPublication): self
    {
        if (!$this->commentsPublications->contains($commentsPublication)) {
            $this->commentsPublications[] = $commentsPublication;
            $commentsPublication->setPublication($this);
        }

        return $this;
    }

    public function removeCommentsPublication(CommentsPublication $commentsPublication): self
    {
        if ($this->commentsPublications->contains($commentsPublication)) {
            $this->commentsPublications->removeElement($commentsPublication);
            // set the owning side to null (unless already changed)
            if ($commentsPublication->getPublication() === $this) {
                $commentsPublication->setPublication(null);
            }
        }

        return $this;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(string $page): self
    {
        $this->page = $page;

        return $this;
    }
}
