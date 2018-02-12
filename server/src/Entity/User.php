<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Security\SecureToken;
use Closure;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements as AssertPassword;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as AssertUnique;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 *
 * @package App\Entity
 * @ApiResource()
 * @AssertUnique(fields={"username"})
 * @AssertUnique(fields={"emailAddress"})
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User implements UserInterface
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     * @Groups("read")
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column(name="firstname", type="string", nullable=true)
     * @Groups({"read", "write"})
     */
    protected $firstname;

    /**
     * @var string
     * @Assert\NotNull()
     * @ORM\Column(name="lastname", type="string", nullable=true)
     * @Groups({"read", "write"})
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
     * @Groups({"read", "write"})
     */
    protected $username;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\Email(strict=true, checkHost=true, checkMX=true)
     * @ORM\Column(name="email_address", type="string", unique=true)
     * @Groups({"read", "write"})
     */
    protected $emailAddress;

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
     * @var bool
     * @ORM\Column(name="enabled", type="boolean")
     * @Groups({"read", "write"})
     */
    protected $enabled;

    /**
     * @var bool
     * @ORM\Column(name="locked", type="boolean")
     * @Groups({"read"})
     */
    protected $locked;

    /**
     * @var string[]
     * @ORM\Column(name="roles", type="array")
     * @Groups({"read", "write"})
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
     * @Groups("write")
     */
    protected $plainPassword;

    /**
     * @var ArrayCollection
     * @ApiSubresource()
     * @ORM\OneToMany(targetEntity="UserEvent", mappedBy="user", cascade={"persist", "remove"})
     * @Groups("read")
     */
    protected $userEvents;

    /**
     * User constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->salt = SecureToken::generate();
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
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param null|string $password
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
        return \in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
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
     * @return null|string
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
     * @return null|string
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param null|string $confirmationToken
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
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $boolean
     * @return $this
     */
    public function setEnabled(bool $boolean): self
    {
        $this->enabled = $boolean;

        return $this;
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
    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     * @return $this
     */
    public function setEmailAddress(string $emailAddress): self
    {
        if ($emailAddress) {
            $emailAddress = strtolower($emailAddress);
        }
        $this->emailAddress = strtolower($emailAddress);

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
        if ($event->getUser() !== $this) {
            $event->setUser($this);
        }

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
