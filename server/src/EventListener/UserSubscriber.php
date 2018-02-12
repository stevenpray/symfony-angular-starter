<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserSubscriber
 *
 * @package App\EventListener
 */
class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::prePersist => 'onPrePersist',
            Events::preUpdate  => 'onPreUpdate',
        ];
    }

    /**
     * UserSubscriber constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function onPrePersist(LifecycleEventArgs $args): void
    {
        $user = $args->getEntity();
        if (!$user instanceof User) {
            return;
        }
        /** @var User $user */
        if ($user->getPlainPassword()) {
            $this->encodeUserPassword($user);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function onPreUpdate(LifecycleEventArgs $args): void
    {
        /** @var User $user */
        $user = $args->getEntity();
        if (!$user instanceof User) {
            return;
        }
        if ($user->getPlainPassword()) {
            $this->encodeUserPassword($user);
        }
    }

    /**
     * @param User $user
     */
    protected function encodeUserPassword(User $user): void
    {
        $encoded = $this->encoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($encoded);
        $user->eraseCredentials();
    }
}
