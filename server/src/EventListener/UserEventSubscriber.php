<?php
declare(strict_types=1);

namespace App\EventListener;

use App\DBAL\Types\UserEventType;
use App\Entity\User;
use App\Entity\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function count;

/**
 * Class UserEventSubscriber
 *
 * @package App\EventListener
 */
class UserEventSubscriber implements EventSubscriberInterface
{
    use Timestampable;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var int
     */
    protected $maxAttempts;

    /**
     * UserEventListener constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->maxAttempts = 7;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserEventType::INTERACTIVE_LOGIN_FAILURE => 'onInteractiveLoginFailure',
            UserEventType::INTERACTIVE_LOGIN_SUCCESS => 'onInteractiveLoginSuccess',
        ];
    }

    /**
     * @param UserEvent $event
     * @throws NonUniqueResultException
     */
    public function onInteractiveLoginFailure(UserEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();
        if ($user->isLocked() === false) {
            $failures = $this->em->getRepository(UserEvent::class)->findConsecutiveLoginFailuresByUser($user);
            if (count($failures) >= $this->maxAttempts) {
                $user->setLocked(true);
            }
        }
        $this->em->persist($event);
        $this->em->flush();
    }

    /**
     * @param UserEvent $event
     */
    public function onInteractiveLoginSuccess(UserEvent $event): void
    {
        $this->em->persist($event);
        $this->em->flush();
    }
}
