<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class UserEvent
 *
 * @package App\Entity
 * @ApiResource()
 * @ORM\Entity()
 * @ORM\Table(name="user_events")
 * @ORM\HasLifecycleCallbacks()
 */
class UserEvent extends Event
{
    public const NAME = 'app.event.user';

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
    public function setType($type)
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
    public function setUser(User $user)
    {
        $this->user = $user;
        if ($user->hasUserEvent($this) === false) {
            $user->addUserEvent($this);
        }

        return $this;
    }
}