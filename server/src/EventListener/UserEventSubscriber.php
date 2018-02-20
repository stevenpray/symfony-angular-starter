<?php
declare(strict_types=1);

namespace App\EventListener;

use App\DBAL\Types\UserEventType;
use App\Entity\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    protected $maxLoginAttempts;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * UserEventListener constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManagerInterface $em
     * @param int $maxLoginAttempts
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $em, int $maxLoginAttempts)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->maxLoginAttempts = $maxLoginAttempts;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserEventType::INTERACTIVE_LOGIN_FAILURE => 'onInteractiveLoginFailure',
            UserEventType::INTERACTIVE_LOGIN_SUCCESS => 'onInteractiveLoginSuccess',
            UserEventType::PASSWORD_RESET_SUCCESS    => 'onPasswordResetSuccess',
        ];
    }

    /**
     * @param UserEvent $event
     * @throws NonUniqueResultException
     */
    public function onInteractiveLoginFailure(UserEvent $event): void
    {
        $user = $event->getUser();
        if (!$user->isLocked()) {
            $failures = $this->em->getRepository(UserEvent::class)->findConsecutiveLoginFailuresByUser($user);
            if (count($failures) > $this->maxLoginAttempts) {
                $user->setLocked(true);
                $lockedEvent = new UserEvent(UserEventType::LOCKED, $user);
                $this->dispatcher->dispatch($lockedEvent->getType(), $lockedEvent);
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

    /**
     * @param UserEvent $event
     */
    public function onPasswordResetSuccess(UserEvent $event): void
    {
        $user = $event->getUser();
        $user->setLocked(false);
        $this->em->persist($event);
        $this->em->flush();
    }
}
