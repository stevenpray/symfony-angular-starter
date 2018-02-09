<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Security\RolesProvider;
use function error_log;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class JWTSubscriber
 *
 * @package App\EventListener
 */
class JWTSubscriber implements EventSubscriberInterface
{
    /**
     * @var RolesProvider
     */
    protected $rolesProvider;

    /**
     * JWTSubscriber constructor.
     *
     * @param RolesProvider $rolesProvider
     */
    public function __construct(RolesProvider $rolesProvider)
    {
        $this->rolesProvider = $rolesProvider;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_CREATED => 'onCreated',
        ];
    }

    /**
     * @param JWTCreatedEvent $event
     * @throws ReflectionException
     */
    public function onCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $payload['roles'] = $this->rolesProvider->getReachableRolesBitmask($event->getUser());
        $event->setData($payload);
    }
}
