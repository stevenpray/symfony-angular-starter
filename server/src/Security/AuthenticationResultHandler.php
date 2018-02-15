<?php
declare(strict_types=1);

namespace App\Security;

use App\DBAL\Types\UserEventType;
use App\Entity\User;
use App\Entity\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Class AuthenticationResultHandler
 *
 * @package App\Security
 */
class AuthenticationResultHandler implements AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var JWTManager
     */
    protected $jwtManager;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * AuthenticationHandler constructor.
     *
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     * @param JWTTokenManagerInterface $jwtManager
     */
    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->jwtManager = $jwtManager;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $event = new AuthenticationFailureEvent($exception, new JWTAuthenticationFailureResponse());
        $this->dispatcher->dispatch(Events::AUTHENTICATION_FAILURE, $event);

        $token = $exception->getToken();
        if ($token) {
            $username = $token->getUsername();
            $user = $this->em->getRepository(User::class)->findOneBy(['username' => strtolower($username)]);
            if ($user) {
                $userEvent = new UserEvent(UserEventType::INTERACTIVE_LOGIN_FAILURE);
                $user->addUserEvent($userEvent);
                $this->dispatcher->dispatch($userEvent->getType(), $userEvent);
            }
        }

        return $event->getResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();

        $userEvent = new UserEvent(UserEventType::INTERACTIVE_LOGIN_SUCCESS);
        $user->addUserEvent($userEvent);
        $this->dispatcher->dispatch($userEvent->getType(), $userEvent);

        $jwt = $this->jwtManager->create($user);
        $response = new JWTAuthenticationSuccessResponse($jwt);
        $successEvent = new AuthenticationSuccessEvent(['token' => $jwt], $user, $response);
        $this->dispatcher->dispatch(Events::AUTHENTICATION_SUCCESS, $successEvent);

        $response->setData($successEvent->getData());

        return $response;
    }
}
