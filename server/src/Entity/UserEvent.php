<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class UserEvent
 *
 * @package App\Entity
 * @ApiResource(
 *  attributes={"access_control"="is_granted('ROLE_USER')"},
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
     * @Groups("read")
     */
    protected $id;

    /**
     * @var DateTime $createdAt
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var string
     * @ORM\Column(name="type", type="user_event_type")
     * @Groups("read")
     */
    protected $type;

    /**
     * @var User
     * @ApiSubresource()
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userEvents", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id")
     */
    protected $user;

    /**
     * UserEvent constructor.
     *
     * @param string $type
     */
    public function __construct($type = null)
    {
        $this->type = $type;
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
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        if ($user->hasUserEvent($this) === false) {
            $user->addUserEvent($this);
        }

        return $this;
    }
}
