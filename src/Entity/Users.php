<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'email entrez est déjà utilisé"
 * )
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $apropos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $work;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit faire minimum 8 caractères")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Le mot de passe entrez ne correspond pas à celui d'au desssus")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rank;

    /**
     * @ORM\Column(type="integer")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Articles", mappedBy="users", orphanRemoval=true)
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="users", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PublicationsProfil", mappedBy="users", orphanRemoval=true)
     */
    private $publicationsProfils;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommentsPublication", mappedBy="users", orphanRemoval=true)
     */
    private $commentsPublications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LikeArticle", mappedBy="users", orphanRemoval=true)
     */
    private $likeArticles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $verif;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->publicationsProfils = new ArrayCollection();
        $this->commentsPublications = new ArrayCollection();
        $this->likeArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getApropos(): ?string
    {
        return $this->apropos;
    }

    public function setApropos(string $apropos): self
    {
        $this->apropos = $apropos;

        return $this;
    }

    public function getWork(): ?string
    {
        return $this->work;
    }

    public function setWork(?string $work): self
    {
        $this->work = $work;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(string $rank): self
    {
        $this->rank = $rank;

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

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        if ($this->rank == "ROLE_USER") {
            return ["ROLE_USER"];
        } elseif ($this->rank == "ROLE_ADMIN") {
            return ["ROLE_ADMIN"];
        } else {
            return [];
        }
    }

    public function getSalt()
    {
    }

    /**
     * @return Collection|Articles[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setUsers($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getUsers() === $this) {
                $article->setUsers(null);
            }
        }

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
            $comment->setUsers($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUsers() === $this) {
                $comment->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PublicationsProfil[]
     */
    public function getPublicationsProfils(): Collection
    {
        return $this->publicationsProfils;
    }

    public function addPublicationsProfil(PublicationsProfil $publicationsProfil): self
    {
        if (!$this->publicationsProfils->contains($publicationsProfil)) {
            $this->publicationsProfils[] = $publicationsProfil;
            $publicationsProfil->setUsers($this);
        }

        return $this;
    }

    public function removePublicationsProfil(PublicationsProfil $publicationsProfil): self
    {
        if ($this->publicationsProfils->contains($publicationsProfil)) {
            $this->publicationsProfils->removeElement($publicationsProfil);
            // set the owning side to null (unless already changed)
            if ($publicationsProfil->getUsers() === $this) {
                $publicationsProfil->setUsers(null);
            }
        }

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
            $commentsPublication->setUsers($this);
        }

        return $this;
    }

    public function removeCommentsPublication(CommentsPublication $commentsPublication): self
    {
        if ($this->commentsPublications->contains($commentsPublication)) {
            $this->commentsPublications->removeElement($commentsPublication);
            // set the owning side to null (unless already changed)
            if ($commentsPublication->getUsers() === $this) {
                $commentsPublication->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LikeArticle[]
     */
    public function getLikeArticles(): Collection
    {
        return $this->likeArticles;
    }

    public function addLikeArticle(LikeArticle $likeArticle): self
    {
        if (!$this->likeArticles->contains($likeArticle)) {
            $this->likeArticles[] = $likeArticle;
            $likeArticle->setUsers($this);
        }

        return $this;
    }

    public function removeLikeArticle(LikeArticle $likeArticle): self
    {
        if ($this->likeArticles->contains($likeArticle)) {
            $this->likeArticles->removeElement($likeArticle);
            // set the owning side to null (unless already changed)
            if ($likeArticle->getUsers() === $this) {
                $likeArticle->setUsers(null);
            }
        }

        return $this;
    }

    public function getVerif(): ?string
    {
        return $this->verif;
    }

    public function setVerif(?string $verif): self
    {
        $this->verif = $verif;

        return $this;
    }
}
