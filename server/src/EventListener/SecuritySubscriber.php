<?php
declare(strict_types=1);

namespace App\EventListener;

use App\DBAL\Types\UserEventType;
use App\Entity\User;
use App\Entity\UserEvent;
use App\Security\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Class SecuritySubscriber
 *
 * @package App\EventListener
 */
class SecuritySubscriber implements EventSubscriberInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * SecuritySubscriber constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManagerInterface $em
     * @param UserProvider $userProvider
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em,
        UserProvider $userProvider
    ) {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->userProvider = $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
            SecurityEvents::INTERACTIVE_LOGIN            => 'onInteractiveLogin',
        ];
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $token = $event->getAuthenticationToken();
        if ($token) {
            $username = $token->getUsername();
            $user = $this->em->getRepository(User::class)->findOneBy(['username' => strtolower($username)]);
            if ($user) {
                $userEvent = new UserEvent(UserEventType::INTERACTIVE_LOGIN_FAILURE);
                $user->addUserEvent($userEvent);
                $this->dispatcher->dispatch($userEvent->getType(), $userEvent);
            }
        }
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $userEvent = new UserEvent(UserEventType::INTERACTIVE_LOGIN_SUCCESS);
        $user->addUserEvent($userEvent);
        $this->dispatcher->dispatch($userEvent->getType(), $userEvent);
    }
}
