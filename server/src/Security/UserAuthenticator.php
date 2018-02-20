<?php
declare(strict_types=1);

namespace App\Security;

use App\DBAL\Types\UserEventType;
use App\Entity\User;
use App\Entity\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use function get_class;
use function gettype;
use function is_string;
use function sprintf;
use function strlen;
use function trim;

/**
 * Class UserAuthenticator
 *
 * @package App\Security
 */
class UserAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var UserPasswordEncoder
     */
    protected $encoder;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var JWTTokenManagerInterface
     */
    protected $jm;

    /**
     * UserAuthenticator constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     * @param JWTTokenManagerInterface $jm
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        JWTTokenManagerInterface $jm
    ) {
        $this->dispatcher = $dispatcher;
        $this->encoder = $encoder;
        $this->em = $em;
        $this->jm = $jm;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        /** @var User $user */
        if ($this->encoder->isPasswordValid($user, $credentials['password'])) {
            $event = new UserEvent(UserEventType::INTERACTIVE_LOGIN_SUCCESS, $user);
            $this->dispatcher->dispatch($event->getType(), $event);

            return true;
        }
        $event = new UserEvent(UserEventType::INTERACTIVE_LOGIN_FAILURE, $user);
        $this->dispatcher->dispatch($event->getType(), $event);
        throw new BadCredentialsException('Invalid credentials.');
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request): array
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        if (!is_string($password)) {
            throw new BadRequestHttpException(\sprintf('Password must be a string, "%s" given.', gettype($username)));
        }
        if (!is_string($username)) {
            throw new BadRequestHttpException(\sprintf('Username must be a string, "%s" given.', gettype($username)));
        }
        $username = trim($username);
        if (strlen($username) > Security::MAX_USERNAME_LENGTH) {
            throw new BadCredentialsException('Invalid username.');
        }

        return ['username' => $username, 'password' => $password];
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        if (!$userProvider instanceof UserProvider) {
            throw new InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of %s (%s was given).',
                    UserProvider::class,
                    get_class($userProvider)
                )
            );
        }

        return $userProvider->loadUserByUsername($credentials['username']);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $event = new AuthenticationFailureEvent($exception, new JWTAuthenticationFailureResponse());
        $this->dispatcher->dispatch(Events::AUTHENTICATION_FAILURE, $event);

        return $event->getResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): Response
    {
        $user = $token->getUser();
        $jwt = $this->jm->create($user);
        $response = new JWTAuthenticationSuccessResponse($jwt);
        $event = new AuthenticationSuccessEvent(['token' => $jwt], $user, $response);
        $this->dispatcher->dispatch(Events::AUTHENTICATION_SUCCESS, $event);
        $response->setData($event->getData());

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null): void
    {
        // NOOP.
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
