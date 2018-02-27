<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Security\SecureToken;
use App\Validator\Constraints\Roles as AssertRoles;
use Closure;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteable;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements as AssertPassword;
use RuntimeException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as AssertUnique;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use function array_filter;
use function array_search;
use function array_values;
use function implode;
use function in_array;
use function strtolower;
use function strtoupper;

/**
 * Class User
 *
 * @package App\Entity
 * @ApiResource(
 *  attributes={
 *     "access_control"="is_granted('ROLE_ADMIN')",
 *     "normalization_context"={"groups"={"read"}},
 *     "denormalization_context"={"groups"={"write"}}
 *  }
 * )
 * @AssertUnique(fields={"username"})
 * @AssertUnique(fields={"email"})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements AdvancedUserInterface
{
    use SoftDeleteable;

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     * @Serializer\Groups({"read"})
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column(name="firstname", type="string", nullable=true)
     * @Serializer\Groups({"read", "write"})
     */
    protected $firstname;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column(name="lastname", type="string", nullable=true)
     * @Serializer\Groups({"read", "write"})
     */
    protected $lastname;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\Length(min="2", max="255")
     * @Assert\Regex(
     *  pattern="/^[A-Za-z0-9_-]+$/u",
     *  message="This value contains invalid characters.",
     * )
     * @ORM\Column(name="username", type="string", unique=true)
     * @Serializer\Groups({"read", "write"})
     */
    protected $username;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\Email(strict=true, checkHost=true, checkMX=true)
     * @ORM\Column(name="email", type="string", unique=true)
     * @Serializer\Groups({"read", "write"})
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="password", nullable=true)
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(name="salt")
     */
    protected $salt;

    /**
     * @var string
     * @ORM\Column(name="confirmation_token", type="string", nullable=true, unique=true, length=31)
     */
    protected $confirmationToken;

    /**
     * @var DateTimeInterface
     * @ORM\Column(name="confirmation_created_at", type="datetime", nullable=true)
     */
    protected $confirmationCreatedAt;

    /**
     * @var DateTimeInterface
     * @ORM\Column(name="password_expires_at", type="datetime", nullable=true)
     * @Serializer\Groups({"read", "write"})
     */
    protected $passwordExpiresAt;

    /**
     * @var DateTime $createdAt
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     * @Serializer\Groups({"read"})
     */
    protected $createdAt;

    /**
     * @var DateTime $updatedAt
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     * @Serializer\Groups({"read"})
     */
    protected $updatedAt;

    /**
     * @var DateTime
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     * @Serializer\Groups({"read", "write"})
     */
    protected $deletedAt;

    /**
     * @var bool
     * @ORM\Column(name="enabled", type="boolean")
     * @Serializer\Groups({"read", "write"})
     */
    protected $enabled;

    /**
     * @var bool
     * @ORM\Column(name="locked", type="boolean")
     * @Serializer\Groups({"read"})
     */
    protected $locked;

    /**
     * @var string[]
     * @AssertRoles()
     * @ORM\Column(name="roles", type="array")
     * @Serializer\Groups({"read", "write"})
     */
    protected $roles;

    /**
     * @var string
     * @Assert\Length(max=4096))
     * @AssertPassword(
     *  minLength=8,
     *  requireCaseDiff=true,
     *  requireLetters=true,
     *  requireNumbers=true,
     *  requireSpecialCharacter=true
     * )
     * @Serializer\Groups("write")
     */
    protected $plainPassword;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="UserEvent", mappedBy="user", cascade={"persist", "remove"})
     * @Serializer\Groups({"read"})
     */
    protected $userEvents;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->enabled = true;
        $this->locked = false;
        $this->roles = ['ROLE_USER'];
        $this->userEvents = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = strtolower($username);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return $this
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = [];
        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function addRole(string $role): self
    {
        if ($this->hasRole($role) === false) {
            $this->roles[] = strtoupper($role);
        }

        return $this;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        if ($this->salt === null) {
            try {
                $this->salt = SecureToken::generate();
            } catch (Exception $exception) {
                throw new RuntimeException('Unable to generate user salt', 0, $exception);
            }
        }

        return $this->salt;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked(): bool
    {
        return !$this->isLocked();
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     * @return $this
     */
    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired(): bool
    {
        return !$this->isPasswordExpired();
    }

    /**
     * @return bool
     */
    public function isPasswordExpired(): bool
    {
        if (!$this->getPasswordExpiresAt()) {
            return false;
        }

        return $this->getPasswordExpiresAt() <= new DateTime();
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPasswordExpiresAt(): ?DateTimeInterface
    {
        return $this->passwordExpiresAt;
    }

    /**
     * @param DateTimeInterface|null $passwordExpiresAt
     * @return $this
     */
    public function setPasswordExpiresAt(?DateTimeInterface $passwordExpiresAt): self
    {
        $this->passwordExpiresAt = $passwordExpiresAt;

        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function removeRole(string $role): self
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPlainPassword(string $password): self
    {
        $this->plainPassword = $password;
        $this->setPassword(null);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param string|null $confirmationToken
     * @return $this
     */
    public function setConfirmationToken(?string $confirmationToken): self
    {
        if ($confirmationToken === null) {
            $this->confirmationCreatedAt = null;
        } else {
            if ($confirmationToken !== $this->confirmationToken) {
                $this->confirmationCreatedAt = new DateTime();
            }
        }
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getConfirmationCreatedAt(): ?DateTimeInterface
    {
        return $this->confirmationCreatedAt;
    }

    /**
     * @param DateTimeInterface|null $date
     * @return $this
     */
    public function setConfirmationCreatedAt(?DateTimeInterface $date): self
    {
        $this->confirmationCreatedAt = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = strtolower($email);

        return $this;
    }

    /**
     * @return string
     */
    public function getFullname(): string
    {
        return implode(' ', array_filter([$this->getFirstname(), $this->getLastname()]));
    }

    /**
     * @return string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return $this
     */
    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return $this
     */
    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @param Closure|null $filter
     * @return UserEvent[]
     */
    public function getUserEvents(Closure $filter = null): array
    {
        $items = $this->userEvents;
        if ($filter) {
            $items = $items->filter($filter);
        }

        return $items->toArray();
    }

    /**
     * @param UserEvent[] $events
     * @return $this
     */
    public function setUserEvents(array $events): self
    {
        foreach ($events as $event) {
            $this->addUserEvent($event);
        }

        return $this;
    }

    /**
     * @param UserEvent $event
     * @return $this
     */
    public function addUserEvent(UserEvent $event): self
    {
        $this->userEvents->add($event);

        return $this;
    }

    /**
     * @param UserEvent $event
     * @return bool
     */
    public function hasUserEvent(UserEvent $event): bool
    {
        return $this->userEvents->contains($event);
    }
}
