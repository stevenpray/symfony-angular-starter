<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Security\RolesProvider;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * JWTSubscriber constructor.
     *
     * @param RolesProvider $rolesProvider
     * @param EntityManagerInterface $em
     */
    public function __construct(RolesProvider $rolesProvider, EntityManagerInterface $em)
    {
        $this->rolesProvider = $rolesProvider;
        $this->em = $em;
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
