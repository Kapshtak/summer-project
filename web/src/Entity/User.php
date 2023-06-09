<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post()
    ],
    normalizationContext: ['groups' => ['users:read']],
    denormalizationContext: ['groups' => ['users:write']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['users:read', 'events:read', 'comments:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['users:read', 'users:write', 'comments:read', 'events:read'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;


    #[ORM\Column]
    #[Groups(['users:read', 'comments:read', 'events:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['users:write'])]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'ownedBy', targetEntity: ApiToken::class)]
    #[Assert\NotBlank]
    private Collection $apiTokens;


    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Questions::class)]
    private Collection $questions;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comments::class, orphanRemoval: true)]
    #[Groups(['users:read'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: PollsVotes::class, orphanRemoval: true)]
    private Collection $pollsVotes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: EventsUsers::class, orphanRemoval: true)]
    private Collection $eventsUsers;

    public function __construct()
{
    $this->comments = new ArrayCollection();
    $this->questions = new ArrayCollection();
    $this->apiTokens = new ArrayCollection();
    $this->pollsVotes = new ArrayCollection();
    $this->eventsUsers = new ArrayCollection();
}

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): self
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
            $apiToken->setOwnedBy($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): self
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getOwnedBy() === $this) {
                $apiToken->setOwnedBy(null);
            }
        }

        return $this;
    }
  
    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Questions>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Questions $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setAuthor($this);
        }

        return $this;
    }

    public function removeQuestion(Questions $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getAuthor() === $this) {
                $question->setAuthor(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->email;
    }

    /**
     * @return Collection<int, PollsVotes>
     */
    public function getPollsVotes(): Collection
    {
        return $this->pollsVotes;
    }

    public function addPollsVote(PollsVotes $pollsVote): self
    {
        if (!$this->pollsVotes->contains($pollsVote)) {
            $this->pollsVotes->add($pollsVote);
            $pollsVote->setAuthor($this);
        }

        return $this;
    }

    public function removePollsVote(PollsVotes $pollsVote): self
    {
        if ($this->pollsVotes->removeElement($pollsVote)) {
            // set the owning side to null (unless already changed)
            if ($pollsVote->getAuthor() === $this) {
                $pollsVote->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventsUsers>
     */
    public function getEventsUsers(): Collection
    {
        return $this->eventsUsers;
    }

    public function addEventsUser(EventsUsers $eventsUser): self
    {
        if (!$this->eventsUsers->contains($eventsUser)) {
            $this->eventsUsers->add($eventsUser);
            $eventsUser->setUser($this);
        }

        return $this;
    }

    public function removeEventsUser(EventsUsers $eventsUser): self
    {
        if ($this->eventsUsers->removeElement($eventsUser)) {
            // set the owning side to null (unless already changed)
            if ($eventsUser->getUser() === $this) {
                $eventsUser->setUser(null);
            }
        }

        return $this;
    }
}
