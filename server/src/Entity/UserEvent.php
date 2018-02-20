<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * Class UserEvent
 *
 * @package App\Entity
 * @ApiResource(
 *  attributes={
 *     "access_control"="is_granted('ROLE_ADMIN')",
 *     "normalization_context"={"groups"={"read"}},
 *     "denormalization_context"={"groups"={"write"}}
 *  },
 *  collectionOperations={"get"={"method"="GET"}},
 *  itemOperations={"get"={"method"="GET"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserEventRepository")
 * @ORM\Table(name="user_events")
 * @ORM\HasLifecycleCallbacks()
 */
class UserEvent extends Event
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     * @Serializer\Groups("read")
     */
    protected $id;

    /**
     * @var DateTime $createdAt
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     * @Serializer\Groups({"read"})
     */
    protected $createdAt;

    /**
     * @var string
     * @ORM\Column(name="type", type="user_event_type")
     * @Serializer\Groups({"read"})
     */
    protected $type;

    /**
     * @var User
     * @ApiSubresource()
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userEvents", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", onDelete="cascade")
     * @Serializer\Groups({"read"})
     */
    protected $user;

    /**
     * UserEvent constructor.
     *
     * @param string $type
     * @param User $user
     */
    public function __construct(string $type, User $user)
    {
        $this->createdAt = new DateTime();
        $this->setType($type);
        $this->setUser($user);
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
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    protected function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    protected function setUser(User $user): self
    {
        $this->user = $user;
        if ($user->hasUserEvent($this) === false) {
            $user->addUserEvent($this);
        }

        return $this;
    }
}
