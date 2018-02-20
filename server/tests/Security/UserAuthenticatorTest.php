<?php
declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\UserAuthenticator;
use App\Security\UserProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use function array_map;
use function in_array;
use function strlen;

/**
 * Class UserAuthenticator
 *
 * @package App\Tests\Security
 * @covers \App\Security\UserAuthenticator
 */
class UserAuthenticatorTest extends TestCase
{
    protected const VALID_USERNAMES = ['user', 'admin', 'super'];

    /**
     * @var UserAuthenticator
     */
    protected $authenticator;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    protected function setUp()
    {
        /**
         * @var EventDispatcherInterface $dispatcher
         * @var UserPasswordEncoderInterface $encoder
         * @var EntityRepository $er
         * @var EntityManagerInterface $em
         * @var JWTTokenManagerInterface $jm
         */
        $dispatcher = $this->buildDispatcher();
        $encoder = $this->buildUserPasswordEncoder();
        $er = $this->buildEntityRepository();
        $em = $this->buildEntityManager($er);
        $jm = $this->buildJWTManager('secrettoken');

        $this->authenticator = new UserAuthenticator($dispatcher, $encoder, $em, $jm);
        $this->em = $em;
    }

    /**
     * @return MockObject
     */
    public function buildDispatcher(): MockObject
    {
        $mock = $this->getMockBuilder(EventDispatcher::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        $mock->method('dispatch')
             ->with($this->isType(IsType::TYPE_STRING),
                    $this->isInstanceOf(Event::class));

        return $mock;
    }

    /**
     * @return MockObject
     */
    public function buildUserPasswordEncoder(): MockObject
    {
        $mock = $this->getMockBuilder(UserPasswordEncoder::class)
                     ->disableOriginalConstructor()
                     ->setMethods(['isPasswordValid'])
                     ->getMock();

        $mock->method('isPasswordValid')
             ->with($this->isInstanceOf(User::class))
             ->will($this->returnCallback(function (User $user, ?string $password) {
                 return $user->getPassword() === $password;
             }));

        return $mock;
    }

    /**
     * @return MockObject
     */
    public function buildEntityRepository(): MockObject
    {
        $mock = $this->getMockBuilder(EntityRepository::class)
                     ->disableOriginalConstructor()
                     ->setMethods(['find', 'findOneByUsername'])
                     ->getMock();

        $mock->method('findOneByUsername')
             ->with($this->isType(IsType::TYPE_STRING))
             ->will($this->returnCallback(function ($username) {
                 return in_array($username, static::VALID_USERNAMES, true) ? new User() : null;
             }));

        return $mock;
    }

    /**
     * @param EntityRepository $er
     * @return MockObject
     */
    public function buildEntityManager(EntityRepository $er): MockObject
    {
        $mock = $this->getMockBuilder(EntityManager::class)
                     ->disableOriginalConstructor()
                     ->setMethods(['getRepository'])
                     ->getMock();

        $mock->method('getRepository')
             ->with(User::class)
             ->willReturn($er);

        return $mock;
    }

    /**
     * @param string|null $token
     * @return MockObject
     */
    public function buildJWTManager(string $token = null): MockObject
    {
        $mock = $this->getMockBuilder(JWTManager::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        if (null !== $token) {
            $mock->expects($this->any())
                 ->method('create')
                 ->will($this->returnValue('secrettoken'));
        }

        return $mock;
    }

    /**
     * @dataProvider provideRequests
     * @param Request $request
     * @param string $username
     * @param string $password
     */
    public function testGetCredentials(Request $request, $username, $password): void
    {
        if (in_array(null, [$username, $password], true)) {
            $this->expectException(BadRequestHttpException::class);
            $this->authenticator->getCredentials($request);
        } else {
            if (strlen($username) > Security::MAX_USERNAME_LENGTH) {
                $this->expectException(BadCredentialsException::class);
                $this->authenticator->getCredentials($request);
            } else {
                $credentials = $this->authenticator->getCredentials($request);
                $this->assertArrayHasKey('username', $credentials);
                $this->assertArrayHasKey('password', $credentials);
                $this->assertEquals($username, $credentials['username']);
                $this->assertEquals($password, $credentials['password']);
            }
        }
    }

    /**
     * @dataProvider provideCredentials
     * @param string $username
     */
    public function testGetUser($username): void
    {
        $provider = new UserProvider($this->em);
        if (!in_array($username, static::VALID_USERNAMES, true)) {
            $this->expectException(UsernameNotFoundException::class);
            $this->authenticator->getUser(['username' => $username], $provider);
        } else {
            $user = $this->authenticator->getUser(['username' => $username], $provider);
            $this->assertNotNull($user);
        }
    }

    /**
     * @dataProvider provideCredentialsAndUsers
     * @param string[] $credentials
     * @param User $user
     * @param bool $expected
     */
    public function testCheckCredentials(array $credentials, User $user, bool $expected): void
    {
        if (!$expected) {
            $this->expectException(BadCredentialsException::class);
        }
        $valid = $this->authenticator->checkCredentials($credentials, $user);
        $this->assertTrue($valid);
    }

    public function testOnAuthenticationFailure(): void
    {
        $response = $this->authenticator->onAuthenticationFailure(new Request(), new AuthenticationException());
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $content['code']);
        $this->assertEquals('Bad credentials', $content['message']);
    }

    public function testOnAuthenticationSuccess(): void
    {
        /** @var TokenInterface $token */
        $token = $this->buildToken();
        $response = $this->authenticator->onAuthenticationSuccess(new Request(), $token, 'idk');
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertArrayHasKey('token', $content);
        $this->assertEquals('secrettoken', $content['token']);
    }

    /**
     * @return MockObject
     */
    public function buildToken(): MockObject
    {
        $mock = $this->getMockBuilder(JWTUserToken::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        $mock->expects($this->any())
             ->method('getUser')
             ->will($this->returnValue($this->buildUser()));

        return $mock;
    }

    /**
     * @return MockObject
     */
    public function buildUser(): MockObject
    {
        $mock = $this->getMockBuilder(User::class)
                     ->setMethods(['getUsername', 'addUserEvent'])
                     ->getMock();
        $mock->method('getUsername')
             ->will($this->returnValue('username'));
        $mock->method('addUserEvent')
             ->will($this->returnValue($mock));

        return $mock;
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->authenticator->supports(new Request()));
    }

    public function testSupportsRememberMe(): void
    {
        $this->assertFalse($this->authenticator->supportsRememberMe());
    }

    /**
     * @return array[]
     */
    public function provideRequests(): array
    {
        return array_map(
            function (array $credentials) {
                ['username' => $username, 'password' => $password] = $credentials;
                $request = new Request();
                $request->request->set('username', $username);
                $request->request->set('password', $password);

                return [$request, $username, $password];
            },
            $this->provideCredentials()
        );
    }

    /**
     * @return array[]
     */
    public function provideCredentials(): array
    {
        return [
            ['username' => 'user', 'password' => 'user'],
            ['username' => 'user', 'password' => 'wrong_password'],
            ['username' => 'admin', 'password' => 'admin'],
            ['username' => 'super', 'password' => 'super'],
            ['username' => 'user', 'password' => null],
            ['username' => 'non_existing_user', 'password' => 'password'],
        ];
    }

    /**
     * @return array[]
     */
    public function provideCredentialsAndUsers(): array
    {
        return array_map(
            function (array $credentials) {
                ['username' => $username, 'password' => $password] = $credentials;
                $user = new User();
                $user->setUsername($username);
                $user->setPassword($username);

                return [$credentials, $user, $username === $password];
            },
            $this->provideCredentials()
        );
    }
}
