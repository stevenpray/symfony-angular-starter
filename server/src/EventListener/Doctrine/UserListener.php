<?php
declare(strict_types=1);

namespace App\EventListener\Doctrine;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserListener
 *
 * @package App\EventListener\Doctrine
 */
class UserListener
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;

    /**
     * UserListener constructor.
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
    public function prePersist(LifecycleEventArgs $args): void
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
     * @param User $user
     */
    protected function encodeUserPassword(User $user): void
    {
        $encoded = $this->encoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($encoded);
        $user->eraseCredentials();
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
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
}
